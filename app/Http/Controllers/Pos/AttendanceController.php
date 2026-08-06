<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Ingredient;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $baristas = \App\Models\User::where('role', 'barista')->get();
        $today = Carbon::today();

        $todayAttendances = Attendance::whereDate('date', $today)->with('user')->get();
        $ingredients = Ingredient::active()->orderBy('name')->get();

        return view('pos.attendance', compact('baristas', 'todayAttendances', 'ingredients'));
    }

    public function clockIn(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pin' => 'required|digits:6',
            'role_type' => 'required|in:kasir,non_kasir',
        ]);

        $userId = $request->user_id;
        $user = \App\Models\User::find($userId);

        if ($user->pin && $user->pin !== $request->pin) {
            return back()->with('error', 'PIN absensi salah!');
        } elseif (!$user->pin) {
            return back()->with('error', 'PIN absensi belum diatur oleh admin!');
        }

        $today = Carbon::today();

        $existing = Attendance::where('user_id', $userId)->whereDate('date', $today)->first();
        if ($existing) {
            return back()->with('error', 'Barista tersebut sudah clock in hari ini!');
        }

        $hour = now()->hour;
        $shiftName = 'Pagi';
        if ($hour >= 14 && $hour < 18) {
            $shiftName = 'Siang';
        } elseif ($hour >= 18) {
            $shiftName = 'Malam';
        }

        Attendance::create([
            'user_id' => $userId,
            'clock_in' => now(),
            'status' => 'present',
            'date' => $today,
            'shift_name' => $shiftName,
            'role_type' => $request->role_type,
        ]);

        $roleLabel = $request->role_type === 'kasir' ? 'Kasir' : 'Non-Kasir';
        return back()->with('success', "Clock in berhasil sebagai {$roleLabel}!");
    }

    public function clockOut(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pin' => 'required|digits:6'
        ]);

        $userId = $request->user_id;
        $user = \App\Models\User::find($userId);

        if ($user->pin && $user->pin !== $request->pin) {
            return back()->with('error', 'PIN absensi salah!');
        } elseif (!$user->pin) {
            return back()->with('error', 'PIN absensi belum diatur oleh admin!');
        }

        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $userId)->whereDate('date', $today)->first();
        if (!$attendance) {
            return back()->with('error', 'Barista tersebut belum clock in!');
        }
        if ($attendance->clock_out) {
            return back()->with('error', 'Barista tersebut sudah clock out!');
        }

        $attendance->update([
            'clock_out' => now(),
        ]);

        return back()->with('success', 'Clock out berhasil!');
    }
}
