<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\InventoryMovement;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WasteController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $wasteMovements = InventoryMovement::with('ingredient')
            ->where('type', 'waste')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderByDesc('created_at')
            ->get();

        $totalWasteCost = $wasteMovements->sum('total_cost');
        $totalWasteItems = $wasteMovements->count();

        $wasteByIngredient = $wasteMovements->groupBy('ingredient_id')->map(function ($items) {
            return [
                'name' => $items->first()->ingredient->name ?? 'Unknown',
                'unit' => $items->first()->ingredient->unit ?? '',
                'total_qty' => $items->sum(fn ($m) => abs($m->quantity)),
                'total_cost' => $items->sum('total_cost'),
            ];
        })->sortByDesc('total_cost')->values();

        $ingredients = Ingredient::active()->orderBy('name')->get();

        // Available months
        $availableMonths = InventoryMovement::where('type', 'waste')
            ->selectRaw("DISTINCT DATE_FORMAT(created_at, '%Y-%m') as month")
            ->orderByDesc('month')
            ->pluck('month')
            ->toArray();

        if (!in_array($month, $availableMonths)) {
            $availableMonths[] = $month;
            rsort($availableMonths);
        }

        return view('admin.waste.index', compact(
            'wasteMovements', 'month', 'totalWasteCost', 'totalWasteItems',
            'wasteByIngredient', 'ingredients', 'availableMonths'
        ));
    }

    public function store(Request $request, InventoryService $inventoryService)
    {
        $validated = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        $ingredient = Ingredient::findOrFail($validated['ingredient_id']);

        $inventoryService->recordWaste(
            $ingredient,
            (float) $validated['quantity'],
            $validated['notes'] ?? 'Bahan terbuang',
            auth()->id()
        );

        return back()->with('success', "Waste {$ingredient->name} berhasil dicatat!");
    }
}
