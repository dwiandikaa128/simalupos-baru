<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Stock;
use App\Models\CashExpense;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $todayGrossSales = Order::paid()->whereDate('paid_at', $today)->sum('total_amount');
        $todayExpenses = CashExpense::whereDate('created_at', $today)->sum('amount');
        $todayRevenue = $todayGrossSales - $todayExpenses;
        $todayTransactions = Order::paid()->whereDate('paid_at', $today)->count();
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $activeBaristas = User::where('role', 'barista')->where('is_active', true)->count();

        // Top products today
        $topProducts = Product::withCount(['orderItems as sold_today' => function ($q) use ($today) {
            $q->whereHas('order', function ($oq) use ($today) {
                $oq->paid()->whereDate('paid_at', $today);
            });
        }])->orderByDesc('sold_today')->take(5)->get();

        // 7-day sales chart (net of expenses)
        $chartData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $sales = Order::paid()->whereDate('paid_at', $date)->sum('total_amount');
            $expenses = CashExpense::whereDate('created_at', $date)->sum('amount');
            $chartData->push([
                'label' => $date->format('D'),
                'date' => $date->format('d/m'),
                'value' => (float) ($sales - $expenses),
            ]);
        }
        $maxChart = max($chartData->pluck('value')->max(), 1);

        // Recent orders
        $recentOrders = Order::with('user')->latest()->take(10)->get();

        // Low stock alerts
        $lowStocks = Stock::where('current_qty', '<=', \DB::raw('min_qty'))->get();

        return view('admin.dashboard.index', compact(
            'todayRevenue', 'todayExpenses', 'todayTransactions', 'todayOrders', 'activeBaristas',
            'topProducts', 'chartData', 'maxChart', 'recentOrders', 'lowStocks'
        ));
    }
}

