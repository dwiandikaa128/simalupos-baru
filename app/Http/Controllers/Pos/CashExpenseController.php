<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\CashExpense;
use App\Models\Ingredient;
use App\Models\InventoryMovement;
use App\Models\Shift;
use App\Models\ActivityLog;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashExpenseController extends Controller
{
    public function store(Request $request, InventoryService $inventoryService)
    {
        $user = auth()->user();
        $shift = Shift::where('user_id', $user->id)->where('status', 'active')->first();

        if (!$shift) {
            return back()->with('error', 'Tidak ada shift aktif! Buka shift terlebih dahulu.');
        }

        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'ingredient_id' => 'nullable|exists:ingredients,id',
            'purchase_qty' => 'nullable|required_with:ingredient_id|numeric|min:0.01',
            'purchase_unit' => 'nullable|required_with:ingredient_id|string|max:20',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $shift, $user, $inventoryService) {
            $expense = CashExpense::create([
                'shift_id' => $shift->id,
                'user_id' => $user->id,
                'description' => $request->description,
                'amount' => $request->amount,
                'ingredient_id' => $request->ingredient_id,
                'purchase_qty' => $request->purchase_qty,
                'purchase_unit' => $request->purchase_unit,
                'notes' => $request->notes,
            ]);

            // If ingredient is selected, add stock via purchase
            if ($request->ingredient_id && $request->purchase_qty) {
                $ingredient = Ingredient::findOrFail($request->ingredient_id);

                $inventoryService->recordPurchase($ingredient, [
                    'quantity' => (float) $request->purchase_qty,
                    'total_cost' => (float) $request->amount,
                    'supplier' => 'Kas Keluar Shift',
                    'notes' => 'Pembelian via kas keluar: ' . $request->description,
                    'purchased_at' => now(),
                ]);
            }

            // Update total expenses on the shift
            $shift->update([
                'total_expenses' => $shift->expenses()->sum('amount'),
            ]);

            $logDesc = "Kas keluar: {$request->description} - " . format_rupiah($request->amount);
            if ($request->ingredient_id) {
                $ingredient = Ingredient::find($request->ingredient_id);
                $logDesc .= " (Beli {$ingredient->name}: {$request->purchase_qty} {$request->purchase_unit})";
            }
            ActivityLog::log('cash_expense', $logDesc, $expense);
        });

        return back()->with('success', 'Kas keluar berhasil dicatat!' . ($request->ingredient_id ? ' Stok bahan telah ditambahkan.' : ''));
    }

    public function destroy(CashExpense $cashExpense)
    {
        $user = auth()->user();
        $shift = $cashExpense->shift;

        if ($shift->status !== 'active') {
            return back()->with('error', 'Tidak bisa menghapus pengeluaran pada shift yang sudah ditutup!');
        }

        $desc = $cashExpense->description;
        $amount = $cashExpense->amount;

        DB::transaction(function () use ($cashExpense, $shift, $desc, $amount) {
            // If linked to ingredient purchase, rollback the stock (Opsi A)
            if ($cashExpense->isIngredientPurchase()) {
                $ingredient = $cashExpense->ingredient;
                if ($ingredient) {
                    $ingredient = Ingredient::lockForUpdate()->find($ingredient->id);
                    if ($ingredient) {
                        $qtyBefore = (float) $ingredient->current_qty;
                        $rollbackQty = (float) $cashExpense->purchase_qty;
                        $qtyAfter = max(0, $qtyBefore - $rollbackQty);

                        $ingredient->update(['current_qty' => $qtyAfter]);

                        InventoryMovement::create([
                            'ingredient_id' => $ingredient->id,
                            'type' => 'adjustment',
                            'quantity' => -$rollbackQty,
                            'unit_cost' => $rollbackQty > 0 ? $amount / $rollbackQty : 0,
                            'total_cost' => $amount,
                            'qty_before' => $qtyBefore,
                            'qty_after' => $qtyAfter,
                            'notes' => "Rollback kas keluar dihapus: {$desc}",
                        ]);
                    }
                }
            }

            $cashExpense->delete();

            // Update total expenses on the shift
            $shift->update([
                'total_expenses' => $shift->expenses()->sum('amount'),
            ]);

            ActivityLog::log('delete_expense', "Hapus kas keluar: {$desc} - " . format_rupiah($amount));
        });

        return back()->with('success', 'Kas keluar berhasil dihapus!' . ($cashExpense->isIngredientPurchase() ? ' Stok bahan telah dikurangi kembali.' : ''));
    }
}
