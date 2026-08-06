<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\Attendance;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::with(['user', 'expenses.ingredient'])->latest('started_at')->paginate(20);

        // For shifts without employee_name, resolve from attendance (absensi) data
        // Exclude the shared login account (shift's user_id) — only show actual barista names
        foreach ($shifts as $shift) {
            if (!$shift->employee_name) {
                $attendanceNames = Attendance::whereDate('date', $shift->started_at->toDateString())
                    ->where('user_id', '!=', $shift->user_id)
                    ->whereHas('user', fn($q) => $q->where('role', 'barista'))
                    ->with('user:id,name')
                    ->get()
                    ->pluck('user.name')
                    ->filter()
                    ->unique()
                    ->implode(', ');

                if ($attendanceNames) {
                    $shift->update(['employee_name' => $attendanceNames]);
                    $shift->employee_name = $attendanceNames;
                }
            }
        }

        return view('admin.shifts.index', compact('shifts'));
    }

    public function show(Shift $shift)
    {
        $shift->load('user', 'attendances');
        return view('admin.shifts.index', compact('shift'));
    }
}
