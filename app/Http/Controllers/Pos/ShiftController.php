<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\Order;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $activeShift = $user->activeShift();
        
        $lastClosedShift = Shift::where('status', 'closed')->latest('ended_at')->first();

        // Load actual barista employees (exclude the shared login account)
        $baristas = \App\Models\User::where('role', 'barista')
            ->where('is_active', true)
            ->where('id', '!=', $user->id)
            ->get();

        // Load expenses for active shift with ingredient relationship
        $expenses = $activeShift ? $activeShift->expenses()->with('ingredient')->latest()->get() : collect();
        $totalExpenses = $expenses->sum('amount');

        // Load ingredients for kas keluar form
        $ingredients = \App\Models\Ingredient::active()->orderBy('name')->get();
        
        return view('pos.shifts', compact('activeShift', 'lastClosedShift', 'baristas', 'expenses', 'totalExpenses', 'ingredients'));
    }

    public function open(Request $request)
    {
        $user = auth()->user();

        // Check for existing active shift
        $existing = Shift::where('user_id', $user->id)->where('status', 'active')->first();
        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki shift aktif!');
        }

        $request->validate([
            'opening_cash' => 'required|numeric|min:0',
            'employee_name' => 'required|string|max:100',
        ]);

        $hour = now()->format('H');
        $shiftName = ($hour >= 7 && $hour < 16) ? 'Pagi' : 'Sore';

        $shift = Shift::create([
            'user_id' => $user->id,
            'employee_name' => $request->employee_name,
            'shift_name' => $shiftName,
            'started_at' => now(),
            'opening_cash' => $request->opening_cash,
            'status' => 'active',
        ]);

        // Auto clock-in
        \App\Models\Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => now()->toDateString()],
            ['clock_in' => now(), 'status' => 'present']
        );

        ActivityLog::log('open_shift', "Membuka shift: {$shift->shift_name}", $shift);

        return back()->with('success', 'Shift berhasil dibuka!');
    }

    public function close(Request $request)
    {
        $user = auth()->user();

        $shift = Shift::where('user_id', $user->id)->where('status', 'active')->first();
        if (!$shift) {
            return back()->with('error', 'Tidak ada shift aktif!');
        }

        $request->validate([
            'actual_closing_cash' => 'required|numeric|min:0',
            'net_revenue' => 'required|numeric|min:0',
            'cash_left_for_next_shift' => 'required|numeric|min:0',
        ]);

        // Calculate total sales during this shift from system
        $totalSales = Order::where('user_id', $user->id)
            ->paid()
            ->whereBetween('paid_at', [$shift->started_at, now()])
            ->sum('total_amount');

        $cashSales = Order::where('user_id', $user->id)
            ->paid()
            ->where('payment_method', 'cash')
            ->whereBetween('paid_at', [$shift->started_at, now()])
            ->sum('total_amount');

        $totalTransactions = Order::where('user_id', $user->id)
            ->paid()
            ->whereBetween('paid_at', [$shift->started_at, now()])
            ->count();

        // Total expenses (kas keluar) during this shift
        $totalExpenses = $shift->expenses()->sum('amount');
            
        // System closing cash = opening + cash sales - expenses
        $systemClosingCash = $shift->opening_cash + $cashSales - $totalExpenses;

        $shift->update([
            'ended_at' => now(),
            'closing_cash' => $systemClosingCash,
            'actual_closing_cash' => $request->actual_closing_cash,
            'net_revenue' => $request->net_revenue,
            'cash_left_for_next_shift' => $request->cash_left_for_next_shift,
            'total_sales' => $totalSales,
            'total_expenses' => $totalExpenses,
            'total_transactions' => $totalTransactions,
            'notes' => $request->notes,
            'status' => 'closed',
        ]);

        // Auto clock-out
        $attendance = \App\Models\Attendance::where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->first();
            
        if ($attendance && !$attendance->clock_out) {
            $attendance->update(['clock_out' => now()]);
        }

        ActivityLog::log('close_shift', "Menutup shift: {$shift->shift_name}", $shift);

        return back()->with('success', 'Shift berhasil ditutup!');
    }
}
