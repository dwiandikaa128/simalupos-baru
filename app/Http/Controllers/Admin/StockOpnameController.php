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

}
