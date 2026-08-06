<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PosAuthController extends Controller
{
    public function unlock(Request $request)
    {
        $request->validate([
            'barista_id' => 'required|exists:users,id',
            'pin' => 'required|digits:6',
        ]);

        $user = User::where('id', $request->barista_id)->where('role', 'barista')->firstOrFail();

        if (!$user->pin) {
            return response()->json([
                'success' => false,
                'message' => 'Barista ini belum memiliki PIN absensi. Silakan hubungi Admin.'
            ], 403);
        }

        if ($user->pin !== $request->pin) {
            return response()->json([
                'success' => false,
                'message' => 'PIN salah! Silakan coba lagi.'
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun barista ini tidak aktif.'
            ], 403);
        }

        session(['active_barista_id' => $user->id]);
        
        // Log switch activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'kiosk_login',
            'description' => 'Membuka kunci layar POS (Kasir Aktif)'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Selamat bekerja, ' . $user->name,
            'barista_name' => $user->name
        ]);
    }

    public function lock(Request $request)
    {
        $baristaId = session('active_barista_id');
        if ($baristaId) {
            ActivityLog::create([
                'user_id' => $baristaId,
                'action' => 'kiosk_logout',
                'description' => 'Mengunci layar POS (Ganti Kasir)'
            ]);
        }
        
        session()->forget('active_barista_id');

        return redirect()->route('pos.index');
    }
}
