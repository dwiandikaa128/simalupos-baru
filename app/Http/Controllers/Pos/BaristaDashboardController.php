<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shift;
use App\Models\CashExpense;
use Carbon\Carbon;

class BaristaDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Active shift
        $activeShift = Shift::where('user_id', $user->id)->where('status', 'active')->first();

        // Queue stats
        $pendingOrders = Order::whereIn('status', ['pending', 'processing'])->whereDate('created_at', $today)->count();
        $completedToday = Order::where('status', 'completed')->whereDate('created_at', $today)->count();

        // My stats today (net of expenses)
        $myOrdersToday = Order::where('user_id', $user->id)->paid()->whereDate('paid_at', $today)->count();
        $myGrossSales = Order::where('user_id', $user->id)->paid()->whereDate('paid_at', $today)->sum('total_amount');
        $myExpensesToday = CashExpense::where('user_id', $user->id)->whereDate('created_at', $today)->sum('amount');
        $mySalesToday = $myGrossSales - $myExpensesToday;

        // Recent queue
        $queueOrders = Order::with('items')
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('created_at')
            ->take(10)
            ->get();

        return view('pos.dashboard', compact(
            'activeShift', 'pendingOrders', 'completedToday',
            'myOrdersToday', 'mySalesToday', 'queueOrders'
        ));
    }
}
