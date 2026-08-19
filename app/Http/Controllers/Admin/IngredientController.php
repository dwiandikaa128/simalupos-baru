<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\IngredientPurchase;
use App\Models\InventoryMovement;
use App\Models\AppSetting;
use App\Models\ActivityLog;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class IngredientController extends Controller
{
    public function index()
    {
        $activeCategories = IngredientCategory::active()->orderBy('sort_order')->get();
        $ingredients = Ingredient::with('category')
            ->withCount(['recipes', 'purchases', 'movements'])
            ->orderBy('name')
            ->get();
        $recentPurchases = \App\Models\IngredientPurchase::with('ingredient')
            ->latest('purchased_at')
            ->take(10)
            ->get();

        return view('admin.ingredients.index', compact(
            'activeCategories', 'ingredients', 'recentPurchases'
        ));
    }

    public function categoryIndex()
    {
        $categories = IngredientCategory::withCount('ingredients')->orderBy('sort_order')->get();

        return view('admin.ingredients.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'default_unit' => 'nullable|in:ml,gram,pcs',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['sort_order'] = (IngredientCategory::max('sort_order') ?? 0) + 1;

        IngredientCategory::create($validated);

        return back()->with('success', 'Kategori bahan berhasil ditambahkan!');
    }

    public function updateCategory(Request $request, IngredientCategory $ingredientCategory)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'sort_order' => 'nullable|integer',
            'default_unit' => 'nullable|in:ml,gram,pcs',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        $ingredientCategory->update($validated);

        return back()->with('success', 'Kategori bahan berhasil diupdate!');
    }

    public function destroyCategory(IngredientCategory $ingredientCategory)
    {
        if ($ingredientCategory->ingredients()->count() > 0) {
            return back()->with('error', 'Kategori bahan tidak bisa dihapus karena masih memiliki bahan!');
        }

        $ingredientCategory->delete();

        return back()->with('success', 'Kategori bahan berhasil dihapus!');
    }

    public function storeIngredient(Request $request, InventoryService $inventoryService)
    {
        $validated = $request->validate([
            'ingredient_category_id' => 'required|exists:ingredient_categories,id',
            'name' => 'required|max:150',
            'unit' => 'required|in:ml,gram,pcs',
            'current_qty' => 'nullable|numeric|min:0',
            'min_qty' => 'nullable|numeric|min:0',
            'cost_per_base_unit' => 'nullable|numeric|min:0',
            'track_stock' => 'boolean',
        ]);

        $initialQty = (float) ($validated['current_qty'] ?? 0);
        $validated['cost_per_base_unit'] = $validated['cost_per_base_unit'] ?? 0;
        $validated['current_qty'] = 0;
        $validated['min_qty'] = $validated['min_qty'] ?? 0;
        $validated['track_stock'] = $request->boolean('track_stock', true);

        $ingredient = Ingredient::create($validated);
        if ($initialQty > 0) {
            $inventoryService->recordPurchase($ingredient, [
                'quantity' => $initialQty,
                'notes' => 'Stok awal bahan',
            ]);
        } else {
            $inventoryService->syncLowStockReport($ingredient);
        }

        return back()->with('success', 'Bahan berhasil ditambahkan!');
    }

    public function updateIngredient(Request $request, Ingredient $ingredient, InventoryService $inventoryService)
    {
        $validated = $request->validate([
            'ingredient_category_id' => 'required|exists:ingredient_categories,id',
            'name' => 'required|max:150',
            'unit' => 'required|in:ml,gram,pcs',
            'current_qty' => 'required|numeric|min:0',
            'min_qty' => 'required|numeric|min:0',
            'cost_per_base_unit' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'track_stock' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['track_stock'] = $request->boolean('track_stock', true);
        $validated['cost_per_base_unit'] = $validated['cost_per_base_unit'] ?? 0;
        $ingredient->update($validated);
        $inventoryService->syncLowStockReport($ingredient->fresh());

        return back()->with('success', 'Bahan berhasil diupdate!');
    }

    public function destroyIngredient(Ingredient $ingredient)
    {
        $ingredient->loadCount(['recipes', 'purchases', 'movements']);

        if ($ingredient->recipes_count > 0) {
            return back()->with('error', 'Bahan tidak bisa dihapus karena sudah dipakai di resep menu. Hapus dari resep terlebih dahulu.');
        }

        if ($ingredient->purchases_count > 0 || $ingredient->movements_count > 0) {
            return back()->with('error', 'Bahan tidak bisa dihapus karena sudah memiliki riwayat stok atau pembelian.');
        }

        $ingredient->delete();

        return back()->with('success', 'Bahan berhasil dihapus.');
    }

    public function storePurchase(Request $request, InventoryService $inventoryService)
    {
        $validated = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.01',
            'total_cost' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:150',
            'notes' => 'nullable|string',
            'purchased_at' => 'nullable|date',
        ]);

        $ingredient = Ingredient::findOrFail($validated['ingredient_id']);
        $inventoryService->recordPurchase($ingredient, $validated);

        return back()->with('success', 'Pembelian bahan berhasil dicatat dan stok bertambah!');
    }

    public function destroyPurchase(Request $request, IngredientPurchase $ingredientPurchase)
    {
        $voidPin = AppSetting::get('void_pin');
        if (!empty($voidPin)) {
            if (empty($request->pin) || $request->pin !== $voidPin) {
                return back()->with('error', 'PIN / Password Keamanan (Void) salah atau belum diisi!');
            }
        }

        $ingredient = $ingredientPurchase->ingredient;
        $qtyPurchased = (float) $ingredientPurchase->quantity;
        $cost = (float) $ingredientPurchase->total_cost;

        DB::transaction(function () use ($ingredientPurchase, $ingredient, $qtyPurchased, $cost) {
            if ($ingredient) {
                $ingredient = Ingredient::lockForUpdate()->find($ingredient->id);
                if ($ingredient) {
                    $qtyBefore = (float) $ingredient->current_qty;
                    $qtyAfter = max(0, $qtyBefore - $qtyPurchased);

                    $ingredient->update(['current_qty' => $qtyAfter]);

                    InventoryMovement::create([
                        'ingredient_id' => $ingredient->id,
                        'ingredient_purchase_id' => $ingredientPurchase->id,
                        'type' => 'adjustment',
                        'quantity' => -$qtyPurchased,
                        'unit_cost' => $ingredientPurchase->unit_cost,
                        'total_cost' => $cost,
                        'qty_before' => $qtyBefore,
                        'qty_after' => $qtyAfter,
                        'notes' => "Rollback stok pembelian bahan dihapus: {$ingredient->name}",
                    ]);
                }
            }

            $ingredientPurchase->delete();
            ActivityLog::log('delete_purchase', "Hapus pembelian bahan {$ingredient?->name}: Qty {$qtyPurchased}");
        });

        return back()->with('success', 'Riwayat pembelian/stok masuk berhasil dihapus dan stok telah disesuaikan kembali.');
    }
}
