<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\InventoryMovement;
use App\Models\StockOpname;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $query = StockOpname::with(['ingredient.category', 'user']);

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('opname_date', $request->date);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('opname_date', [$request->from, $request->to]);
        }

        $opnames = $query->orderByDesc('opname_date')
            ->orderByDesc('created_at')
            ->paginate(50)
            ->appends($request->query());

        // Get unique opname dates for quick access
        $opnameDates = StockOpname::selectRaw('opname_date, COUNT(*) as total_items')
            ->groupBy('opname_date')
            ->orderByDesc('opname_date')
            ->limit(30)
            ->get();

        // Stats
        $totalOpnames = StockOpname::count();
        $totalDates = StockOpname::distinct('opname_date')->count('opname_date');
        $lastOpnameDate = StockOpname::max('opname_date');

        return view('admin.stock-opname.index', compact(
            'opnames', 'opnameDates', 'totalOpnames', 'totalDates', 'lastOpnameDate'
        ));
    }

    public function create()
    {
        $ingredients = Ingredient::with('category')->active()->orderBy('name')->get();
        return view('admin.stock-opname.create', compact('ingredients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'opname_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.actual_qty' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['items'] as $item) {
                $ingredient = Ingredient::lockForUpdate()->findOrFail($item['ingredient_id']);
                $systemQty = (float) $ingredient->current_qty;
                $actualQty = (float) $item['actual_qty'];
                $difference = $actualQty - $systemQty;

                $adjustmentType = 'match';
                if ($difference > 0.01) {
                    $adjustmentType = 'surplus';
                } elseif ($difference < -0.01) {
                    $adjustmentType = 'deficit';
                }

                StockOpname::create([
                    'ingredient_id' => $ingredient->id,
                    'user_id' => auth()->id(),
                    'system_qty' => $systemQty,
                    'actual_qty' => $actualQty,
                    'difference' => $difference,
                    'adjustment_type' => $adjustmentType,
                    'notes' => $validated['notes'] ?? null,
                    'opname_date' => $validated['opname_date'],
                ]);

                // Adjust stock if there's a difference
                if ($adjustmentType !== 'match') {
                    $ingredient->update(['current_qty' => $actualQty]);

                    InventoryMovement::create([
                        'ingredient_id' => $ingredient->id,
                        'type' => 'adjustment',
                        'quantity' => $difference,
                        'unit_cost' => $ingredient->unitCost(),
                        'total_cost' => abs($difference) * $ingredient->unitCost(),
                        'qty_before' => $systemQty,
                        'qty_after' => $actualQty,
                        'notes' => "Penyesuaian stock opname: {$adjustmentType} (" . format_qty(abs($difference)) . " {$ingredient->unit})",
                    ]);
                }
            }
        });

        return redirect()->route('admin.stock-opname.index')
            ->with('success', 'Stock opname berhasil disimpan dan stok telah disesuaikan!');
    }
}

