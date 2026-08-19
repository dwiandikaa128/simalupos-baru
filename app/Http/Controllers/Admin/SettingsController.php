<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = AppSetting::all()->pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            // Keep the same group if it exists, else default to 'general'
            $existing = AppSetting::where('key', $key)->first();
            $group = $existing ? $existing->group : 'general';
            
            AppSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '', 'group' => $group]
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil disimpan!');
    }

    public function getWaStatus(\App\Services\WhatsAppNotificationService $service)
    {
        return response()->json($service->getBotStatus());
    }

    public function logoutWaBot(\App\Services\WhatsAppNotificationService $service)
    {
        $success = $service->logoutBot();
        return response()->json(['success' => $success]);
    }
}
