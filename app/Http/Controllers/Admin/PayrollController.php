<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', Carbon::now()->format('Y-m'));

        $payrolls = Payroll::with('user')
            ->forMonth($month)
            ->orderBy('created_at', 'desc')
            ->get();

        $employees = User::where('role', 'barista')->where('is_active', true)->orderBy('name')->get();

        // Summary
        $totalSalary = $payrolls->sum('total_salary');
        $totalPaid = $payrolls->where('payment_status', 'paid')->sum('total_salary');
        $totalPending = $payrolls->where('payment_status', 'pending')->sum('total_salary');

        // Available months
        $availableMonths = Payroll::selectRaw('DISTINCT period_month')
            ->orderByDesc('period_month')
            ->pluck('period_month')
            ->toArray();

        if (!in_array($month, $availableMonths)) {
            $availableMonths[] = $month;
            rsort($availableMonths);
        }

        // Employees without payroll this month (for "add" dropdown)
        $existingUserIds = $payrolls->pluck('user_id')->toArray();
        $employeesWithoutPayroll = $employees->whereNotIn('id', $existingUserIds);

        return view('admin.payroll.index', compact(
            'payrolls', 'month', 'employees', 'employeesWithoutPayroll',
            'totalSalary', 'totalPaid', 'totalPending', 'availableMonths'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'period_month' => 'required|string|size:7',
            'base_salary' => 'required|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'total_working_days' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // Check if payroll already exists
        $exists = Payroll::where('user_id', $validated['user_id'])
            ->where('period_month', $validated['period_month'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Gaji untuk karyawan ini di bulan tersebut sudah ada!');
        }

        // Auto-calculate days present from attendance
        $monthStart = Carbon::parse($validated['period_month'] . '-01')->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $daysPresent = Attendance::where('user_id', $validated['user_id'])
            ->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
            ->where('status', 'present')
            ->count();

        $validated['allowance'] = $validated['allowance'] ?? 0;
        $validated['deduction'] = $validated['deduction'] ?? 0;
        $validated['bonus'] = $validated['bonus'] ?? 0;
        $validated['total_working_days'] = $validated['total_working_days'] ?? $monthEnd->day;
        $validated['days_present'] = $daysPresent;
        $validated['total_salary'] = (float) $validated['base_salary']
            + (float) $validated['allowance']
            - (float) $validated['deduction']
            + (float) $validated['bonus'];
        $validated['payment_status'] = 'pending';

        Payroll::create($validated);

        return back()->with('success', 'Data gaji berhasil ditambahkan!');
    }

    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'base_salary' => 'required|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'total_working_days' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['allowance'] = $validated['allowance'] ?? 0;
        $validated['deduction'] = $validated['deduction'] ?? 0;
        $validated['bonus'] = $validated['bonus'] ?? 0;
        $validated['total_salary'] = (float) $validated['base_salary']
            + (float) $validated['allowance']
            - (float) $validated['deduction']
            + (float) $validated['bonus'];

        $payroll->update($validated);

        return back()->with('success', 'Data gaji berhasil diupdate!');
    }

    public function markAsPaid(Payroll $payroll)
    {
        $payroll->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', "Gaji {$payroll->user->name} ditandai sudah dibayar!");
    }

    public function destroy(Payroll $payroll)
    {
        $month = $payroll->period_month;
        $payroll->delete();
        return redirect()->route('admin.payroll.index', ['month' => $month])
            ->with('success', 'Data gaji berhasil dihapus!');
    }
}
