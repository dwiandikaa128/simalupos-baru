<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('user');

        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->whereMonth('date', $date->month)->whereYear('date', $date->year);
        } else {
            $query->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year);
        }

        $attendances = $query->orderByDesc('date')->paginate(30);
        return view('admin.attendances.index', compact('attendances'));
    }
}
