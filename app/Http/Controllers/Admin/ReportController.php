<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Attendance;
use App\Models\CashExpense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '7days');
        $today = Carbon::today();

        $todaySales = Order::paid()->whereDate('paid_at', $today)->sum('total_amount');
        $todayExpenses = CashExpense::whereDate('created_at', $today)->sum('amount');
        $todayHpp = OrderItem::whereHas('order', fn($q) => $q->paid()->whereDate('paid_at', $today))->sum('total_hpp');
        $todayRevenue = $todaySales - $todayExpenses;
        $todayGrossProfit = $todaySales - $todayHpp;
        $todayTransactions = Order::paid()->whereDate('paid_at', $today)->count();
        $monthSales = Order::paid()->whereMonth('paid_at', $today->month)->whereYear('paid_at', $today->year)->sum('total_amount');
        $monthExpenses = CashExpense::whereMonth('created_at', $today->month)->whereYear('created_at', $today->year)->sum('amount');
        $monthHpp = OrderItem::whereHas('order', fn($q) => $q->paid()->whereMonth('paid_at', $today->month)->whereYear('paid_at', $today->year))->sum('total_hpp');
        $monthRevenue = $monthSales - $monthExpenses;
        $monthGrossProfit = $monthSales - $monthHpp;
        $monthTransactions = Order::paid()->whereMonth('paid_at', $today->month)->whereYear('paid_at', $today->year)->count();

        // Multi-dataset chart data
        $days = $period === '30days' ? 30 : 7;
        $chartLabels = collect();
        $chartSales = collect();
        $chartHpp = collect();
        $chartGrossProfit = collect();
        $chartExpenses = collect();
        $chartNetRevenue = collect();

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $sales = (float) Order::paid()->whereDate('paid_at', $date)->sum('total_amount');
            $hpp = (float) OrderItem::whereHas('order', fn($q) => $q->paid()->whereDate('paid_at', $date))->sum('total_hpp');
            $expenses = (float) CashExpense::whereDate('created_at', $date)->sum('amount');

            $chartLabels->push($date->format('d/m'));
            $chartSales->push($sales);
            $chartHpp->push($hpp);
            $chartGrossProfit->push($sales - $hpp);
            $chartExpenses->push($expenses);
            $chartNetRevenue->push($sales - $expenses);
        }

        // Legacy compatibility
        $chartData = $chartLabels->map(function ($label, $i) use ($chartNetRevenue) {
            return ['label' => $label, 'value' => $chartNetRevenue[$i]];
        });
        $maxChart = max($chartData->pluck('value')->max(), 1);

        $recentOrders = Order::with('user', 'items')->paid()->latest('paid_at')->take(20)->get();

        return view('admin.reports.index', compact(
            'todayRevenue', 'todayTransactions', 'monthRevenue', 'monthTransactions',
            'chartData', 'maxChart', 'recentOrders', 'period',
            'todayHpp', 'todayGrossProfit', 'monthHpp', 'monthGrossProfit',
            'chartLabels', 'chartSales', 'chartHpp', 'chartGrossProfit', 'chartExpenses', 'chartNetRevenue'
        ));
    }

    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::today()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::today()->format('Y-m-d'));

        $orders = Order::with('user', 'items')
            ->paid()
            ->whereBetween('paid_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
            ->latest('paid_at')
            ->paginate(20);

        $totalExpenses = CashExpense::whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])->sum('amount');
        $totalSales = Order::paid()->whereBetween('paid_at', [$startDate, Carbon::parse($endDate)->endOfDay()])->sum('total_amount');
        $totalHpp = OrderItem::whereHas('order', fn($q) => $q->paid()->whereBetween('paid_at', [$startDate, Carbon::parse($endDate)->endOfDay()]))->sum('total_hpp');
        $grossProfit = $totalSales - $totalHpp;
        $totalRevenue = $totalSales - $totalExpenses;
        $totalTransactions = Order::paid()->whereBetween('paid_at', [$startDate, Carbon::parse($endDate)->endOfDay()])->count();

        return view('admin.reports.sales', compact(
            'orders', 'startDate', 'endDate', 'totalRevenue', 'totalTransactions',
            'totalSales', 'totalHpp', 'grossProfit'
        ));
    }

    public function products(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::today()->format('Y-m-d'));
        $categoryId = $request->get('category_id', '');

        // Build product query — only sellable products (from categories table, not ingredients)
        $query = Product::with('category')
            ->withSum(['orderItems as total_sold' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('order', fn($oq) => $oq->paid()
                    ->whereBetween('paid_at', [$startDate, Carbon::parse($endDate)->endOfDay()]));
            }], 'quantity')
            ->withSum(['orderItems as total_revenue' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('order', fn($oq) => $oq->paid()
                    ->whereBetween('paid_at', [$startDate, Carbon::parse($endDate)->endOfDay()]));
            }], 'subtotal')
            ->withSum(['orderItems as total_hpp' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('order', fn($oq) => $oq->paid()
                    ->whereBetween('paid_at', [$startDate, Carbon::parse($endDate)->endOfDay()]));
            }], 'total_hpp');

        // Filter by category
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderByDesc('total_sold')->get();

        // Summary totals
        $totalSold = $products->sum('total_sold');
        $totalRevenue = $products->sum('total_revenue');
        $totalHpp = $products->sum('total_hpp');
        $totalProfit = $totalRevenue - $totalHpp;

        // Categories for filter dropdown
        $categories = Category::active()->orderBy('name')->get();

        return view('admin.reports.products', compact(
            'products', 'categories', 'startDate', 'endDate', 'categoryId',
            'totalSold', 'totalRevenue', 'totalHpp', 'totalProfit'
        ));
    }

    public function employees()
    {
        $employees = User::where('role', 'barista')
            ->withCount(['orders as total_orders' => fn($q) => $q->paid()])
            ->withSum(['orders as total_sales' => fn($q) => $q->paid()], 'total_amount')
            ->get();

        $attendances = Attendance::with('user')
            ->whereMonth('date', Carbon::now()->month)
            ->orderByDesc('date')
            ->get()
            ->groupBy('user_id');

        return view('admin.reports.employees', compact('employees', 'attendances'));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::today()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::today()->format('Y-m-d'));

        $orders = Order::with('user', 'items')
            ->paid()
            ->whereBetween('paid_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
            ->latest('paid_at')
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf', compact('orders', 'startDate', 'endDate'));
        return $pdf->download('laporan-penjualan.pdf');
    }

    public function exportExcel()
    {
        // Placeholder - would need maatwebsite/excel export class
        return back()->with('info', 'Fitur export Excel akan segera tersedia.');
    }
}
