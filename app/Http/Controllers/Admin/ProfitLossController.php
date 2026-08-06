<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CashExpense;
use App\Models\OperationalCost;
use App\Models\Payroll;
use App\Models\InventoryMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProfitLossController extends Controller
{
    public function index(Request $request)
    {
        $periodType = $request->get('type', 'monthly'); // daily, weekly, monthly, or custom
        $today = Carbon::today();

        if ($periodType === 'daily') {
            $date = $request->get('date', $today->format('Y-m-d'));
            $startDate = Carbon::parse($date)->startOfDay();
            $endDate = Carbon::parse($date)->endOfDay();
            $periodLabel = Carbon::parse($date)->translatedFormat('l, d F Y');
            $month = Carbon::parse($date)->format('Y-m');
        } elseif ($periodType === 'weekly') {
            $weekOffset = (int) $request->get('week_offset', 0);
            $startDate = $today->copy()->startOfWeek()->addWeeks($weekOffset);
            $endDate = $startDate->copy()->endOfWeek();
            $periodLabel = $startDate->format('d M') . ' - ' . $endDate->format('d M Y');
            $month = $startDate->format('Y-m');
        } elseif ($periodType === 'custom') {
            $from = $request->get('from', $today->copy()->startOfMonth()->format('Y-m-d'));
            $to = $request->get('to', $today->format('Y-m-d'));
            $startDate = Carbon::parse($from)->startOfDay();
            $endDate = Carbon::parse($to)->endOfDay();
            $periodLabel = Carbon::parse($from)->format('d M Y') . ' — ' . Carbon::parse($to)->format('d M Y');
            $month = Carbon::parse($from)->format('Y-m');
        } else {
            $month = $request->get('month', $today->format('Y-m'));
            $startDate = Carbon::parse($month . '-01')->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $periodLabel = $startDate->translatedFormat('F Y');
        }

        // === PENDAPATAN ===
        $grossSales = Order::paid()
            ->whereBetween('paid_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->sum('total_amount');

        $totalDiscount = Order::paid()
            ->whereBetween('paid_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->sum('discount_amount');

        $totalTax = Order::paid()
            ->whereBetween('paid_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->sum('tax_amount');

        $totalTransactions = Order::paid()
            ->whereBetween('paid_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->count();

        $subtotal = Order::paid()
            ->whereBetween('paid_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->sum('subtotal');

        $netSales = (float) $grossSales;

        // === HPP BAHAN BAKU ===
        $hppBahanBaku = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
            $q->paid()->whereBetween('paid_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
        })->sum('total_hpp');

        // === WASTE COST ===
        $wasteCost = InventoryMovement::where('type', 'waste')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->sum('total_cost');

        // === LABA KOTOR ===
        $grossProfit = $netSales - (float) $hppBahanBaku - (float) $wasteCost;

        // === BIAYA OPERASIONAL ===
        // Prorate monthly costs based on number of days in period vs days in month
        $daysInMonth = $startDate->daysInMonth;
        $daysInPeriod = Carbon::parse($startDate)->startOfDay()->diffInDays(Carbon::parse($endDate)->startOfDay()) + 1;

        if ($periodType === 'monthly') {
            $operationalCosts = OperationalCost::forMonth($month)->get();
            $totalOperational = $operationalCosts->sum('amount');
            $operationalByCategory = $operationalCosts->groupBy('category_label')
                ->map(fn ($items) => $items->sum('amount'));
        } else {
            $ratio = $daysInPeriod / $daysInMonth;
            $operationalCosts = OperationalCost::forMonth($month)->get();
            $totalOperational = $operationalCosts->sum('amount') * $ratio;
            $operationalByCategory = $operationalCosts->groupBy('category_label')
                ->map(fn ($items) => round($items->sum('amount') * $ratio));
        }

        // === GAJI KARYAWAN ===
        if ($periodType === 'monthly') {
            $payrolls = Payroll::forMonth($month)->get();
            $totalPayroll = $payrolls->sum('total_salary');
        } else {
            $ratio = $daysInPeriod / $daysInMonth;
            $payrolls = Payroll::forMonth($month)->get();
            $totalPayroll = $payrolls->sum('total_salary') * $ratio;
        }

        // === PENGELUARAN KAS HARIAN ===
        $totalCashExpenses = CashExpense::whereBetween('created_at', [
            $startDate->startOfDay(), $endDate->endOfDay()
        ])->sum('amount');

        // === TOTAL BIAYA ===
        $totalExpenses = (float) $totalOperational + (float) $totalPayroll + (float) $totalCashExpenses;

        // === LABA BERSIH ===
        $netProfit = $grossProfit - $totalExpenses;
        $netMargin = $netSales > 0 ? ($netProfit / $netSales) * 100 : 0;
        $grossMargin = $netSales > 0 ? ($grossProfit / $netSales) * 100 : 0;

        // === PAYMENT METHOD BREAKDOWN ===
        $paymentBreakdown = Order::paid()
            ->whereBetween('paid_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->get();

        // Available months
        $availableMonths = collect();
        for ($i = 0; $i < 12; $i++) {
            $availableMonths->push($today->copy()->subMonths($i)->format('Y-m'));
        }

        return view('admin.profit-loss.index', compact(
            'periodType', 'periodLabel', 'month', 'startDate', 'endDate',
            'subtotal', 'totalDiscount', 'totalTax', 'netSales', 'totalTransactions',
            'hppBahanBaku', 'wasteCost', 'grossProfit', 'grossMargin',
            'operationalByCategory', 'totalOperational',
            'totalPayroll', 'totalCashExpenses', 'totalExpenses',
            'netProfit', 'netMargin', 'paymentBreakdown', 'availableMonths'
        ));
    }
}
