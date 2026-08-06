<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\StockOpname;

class StockController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::with('category')
            ->active()
            ->where('track_stock', true)
            ->orderBy('name')
            ->get();

        $emptyStocks = $ingredients->filter(fn ($ingredient) => (float) $ingredient->current_qty <= 0);
        $belowMinimumStocks = $ingredients->filter(fn ($ingredient) => (float) $ingredient->current_qty > 0 && (float) $ingredient->current_qty < (float) $ingredient->min_qty);
        $nearMinimumStocks = $ingredients->filter(function ($ingredient) {
            $currentQty = (float) $ingredient->current_qty;
            $minQty = (float) $ingredient->min_qty;

            if ($minQty <= 0 || $currentQty < $minQty) {
                return false;
            }

            return $currentQty <= ($minQty * 1.25);
        });
        $safeStocks = $ingredients->diff($emptyStocks)->diff($belowMinimumStocks)->diff($nearMinimumStocks);

        // Recent stock opname data
        $recentOpnames = StockOpname::with(['ingredient.category', 'user'])
            ->orderByDesc('opname_date')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        $lastOpnameDate = StockOpname::max('opname_date');
        $totalOpnameSessions = StockOpname::distinct('opname_date')->count('opname_date');

        return view('admin.stocks.index', compact(
            'ingredients',
            'safeStocks',
            'nearMinimumStocks',
            'belowMinimumStocks',
            'emptyStocks',
            'recentOpnames',
            'lastOpnameDate',
            'totalOpnameSessions'
        ));
    }
}
