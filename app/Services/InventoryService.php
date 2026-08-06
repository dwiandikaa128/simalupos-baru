<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\IngredientPurchase;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\ProductRecipe;
use App\Models\StockReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function recordPurchase(Ingredient $ingredient, array $data): IngredientPurchase
    {
        return DB::transaction(function () use ($ingredient, $data) {
            $ingredient = Ingredient::lockForUpdate()->findOrFail($ingredient->id);
            $quantity = (float) $data['quantity'];
            $unitCost = $ingredient->unitCost();
            $totalCost = array_key_exists('total_cost', $data)
                ? (float) $data['total_cost']
                : $quantity * $unitCost;
            $unitCost = $quantity > 0 ? $totalCost / $quantity : $unitCost;

            $costBefore = (float) $ingredient->cost_per_base_unit;
            $qtyBefore = (float) $ingredient->current_qty;
            $newQty = $qtyBefore + $quantity;

            $totalValueBefore = $qtyBefore * $costBefore;
            $newAverageCost = $newQty > 0 ? ($totalValueBefore + $totalCost) / $newQty : $costBefore;

            $purchase = IngredientPurchase::create([
                'ingredient_id' => $ingredient->id,
                'user_id' => auth()->id(),
                'quantity' => $quantity,
                'total_cost' => $totalCost,
                'unit_cost' => $unitCost,
                'supplier' => $data['supplier'] ?? null,
                'notes' => $data['notes'] ?? null,
                'purchased_at' => $data['purchased_at'] ?? now(),
            ]);

            $ingredient->update([
                'current_qty' => $newQty,
                'cost_per_base_unit' => $newAverageCost,
            ]);

            InventoryMovement::create([
                'ingredient_id' => $ingredient->id,
                'ingredient_purchase_id' => $purchase->id,
                'type' => 'purchase',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'qty_before' => $qtyBefore,
                'qty_after' => $newQty,
                'notes' => $data['notes'] ?? 'Pembelian bahan',
            ]);

            $this->syncLowStockReport($ingredient->fresh());

            return $purchase;
        });
    }

    public function deductForPaidOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            if (InventoryMovement::where('order_id', $order->id)->where('type', 'sale')->exists()) {
                return;
            }

            $order->loadMissing('items');

            foreach ($order->items as $item) {
                $recipes = ProductRecipe::with('ingredient')
                    ->where('product_id', $item->product_id)
                    ->where(function ($query) use ($item) {
                        $query->whereNull('product_variant_id');
                        if ($item->product_variant_id) {
                            $query->orWhere('product_variant_id', $item->product_variant_id);
                        }
                    })
                    ->get()
                    ->groupBy('ingredient_id');

                $hppPerItem = 0;

                foreach ($recipes as $ingredientId => $recipeRows) {
                    $ingredient = Ingredient::lockForUpdate()->find($ingredientId);
                    if (!$ingredient) {
                        continue;
                    }

                    $qtyPerItem = (float) $recipeRows->sum('quantity');
                    $deductQty = $qtyPerItem * (int) $item->quantity;
                    $unitCost = $ingredient->unitCost();
                    $lineCost = $deductQty * $unitCost;
                    $hppPerItem += $qtyPerItem * $unitCost;

                    // Skip stock deduction for non-tracked ingredients (e.g. Espresso)
                    if (!$ingredient->track_stock) {
                        InventoryMovement::create([
                            'ingredient_id' => $ingredient->id,
                            'order_id' => $order->id,
                            'order_item_id' => $item->id,
                            'type' => 'sale',
                            'quantity' => -$deductQty,
                            'unit_cost' => $unitCost,
                            'total_cost' => $lineCost,
                            'qty_before' => $ingredient->current_qty,
                            'qty_after' => $ingredient->current_qty,
                            'notes' => "Pemakaian resep {$item->product_name} (tanpa deduct stok)",
                        ]);
                        continue;
                    }

                    $qtyBefore = (float) $ingredient->current_qty;
                    // Allow stock to go negative (no validation check here)

                    $qtyAfter = $qtyBefore - $deductQty;
                    $ingredient->update(['current_qty' => $qtyAfter]);

                    InventoryMovement::create([
                        'ingredient_id' => $ingredient->id,
                        'order_id' => $order->id,
                        'order_item_id' => $item->id,
                        'type' => 'sale',
                        'quantity' => -$deductQty,
                        'unit_cost' => $unitCost,
                        'total_cost' => $lineCost,
                        'qty_before' => $qtyBefore,
                        'qty_after' => $qtyAfter,
                        'notes' => "Pemakaian resep {$item->product_name}",
                    ]);

                    $this->syncLowStockReport($ingredient->fresh());
                }

                $totalHpp = $hppPerItem * (int) $item->quantity;
                $item->update([
                    'hpp_per_item' => $hppPerItem,
                    'total_hpp' => $totalHpp,
                    'gross_profit' => (float) $item->subtotal - $totalHpp,
                ]);
            }
        });
    }

    public function restoreForCancelledOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $movements = InventoryMovement::where('order_id', $order->id)->where('type', 'sale')->get();

            foreach ($movements as $movement) {
                $ingredient = Ingredient::lockForUpdate()->find($movement->ingredient_id);
                if (!$ingredient) {
                    continue;
                }

                $restoreQty = abs((float) $movement->quantity);
                if ($restoreQty <= 0) {
                    continue;
                }

                $qtyBefore = (float) $ingredient->current_qty;
                $qtyAfter = $ingredient->track_stock ? $qtyBefore + $restoreQty : $qtyBefore;

                if ($ingredient->track_stock) {
                    $ingredient->update(['current_qty' => $qtyAfter]);
                }

                InventoryMovement::create([
                    'ingredient_id' => $ingredient->id,
                    'order_id' => $order->id,
                    'type' => 'adjustment',
                    'quantity' => $restoreQty,
                    'unit_cost' => $movement->unit_cost,
                    'total_cost' => $movement->total_cost,
                    'qty_before' => $qtyBefore,
                    'qty_after' => $qtyAfter,
                    'notes' => "Pengembalian stok pembatalan pesanan {$order->order_number}",
                ]);

                $this->syncLowStockReport($ingredient->fresh());
            }
        });
    }

    public function recordWaste(Ingredient $ingredient, float $quantity, string $reason, ?int $userId = null): InventoryMovement
    {
        return DB::transaction(function () use ($ingredient, $quantity, $reason, $userId) {
            $ingredient = Ingredient::lockForUpdate()->findOrFail($ingredient->id);

            $qtyBefore = (float) $ingredient->current_qty;
            $unitCost = $ingredient->unitCost();
            $totalCost = $quantity * $unitCost;

            // Deduct stock (allow going negative for tracking purposes)
            $qtyAfter = $qtyBefore - $quantity;
            $ingredient->update(['current_qty' => $qtyAfter]);

            $movement = InventoryMovement::create([
                'ingredient_id' => $ingredient->id,
                'type' => 'waste',
                'quantity' => -$quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'qty_before' => $qtyBefore,
                'qty_after' => $qtyAfter,
                'notes' => $reason,
            ]);

            $this->syncLowStockReport($ingredient->fresh());

            return $movement;
        });
    }

    public function syncLowStockReport(Ingredient $ingredient): void
    {
        if ($ingredient->isLowStock()) {
            $this->createLowStockReportIfNeeded($ingredient);
            return;
        }

        StockReport::where('ingredient_id', $ingredient->id)
            ->where('is_resolved', false)
            ->update(['is_resolved' => true]);
    }

    private function createLowStockReportIfNeeded(Ingredient $ingredient): void
    {
        $hasOpenReport = StockReport::where('ingredient_id', $ingredient->id)
            ->where('is_resolved', false)
            ->exists();

        if ($hasOpenReport) {
            return;
        }

        StockReport::create([
            'ingredient_id' => $ingredient->id,
            'reporter_name' => 'Sistem',
            'item_name' => $ingredient->name,
            'status' => $ingredient->current_qty <= 0 ? 'sudah_habis' : 'mau_habis',
            'notes' => 'Stok '.format_qty($ingredient->current_qty)." {$ingredient->unit}, minimum ".format_qty($ingredient->min_qty)." {$ingredient->unit}.",
            'source' => 'automatic',
            'is_resolved' => false,
        ]);
    }
}
