<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrinterSetting;
use Illuminate\Http\Request;

class PrinterSettingsController extends Controller
{
    public function index()
    {
        $printers = PrinterSetting::all();
        return view('admin.settings.index', compact('printers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'type' => 'required|in:bluetooth,network,usb',
            'address' => 'required|max:100',
            'paper_size' => 'required|in:58mm,80mm',
        ]);

        $validated['is_default'] = $request->boolean('is_default');
        PrinterSetting::create($validated);

        return redirect()->route('admin.settings.index')->with('success', 'Printer berhasil ditambahkan!');
    }

    public function update(Request $request, PrinterSetting $printerSetting)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'type' => 'required|in:bluetooth,network,usb',
            'address' => 'required|max:100',
            'paper_size' => 'required|in:58mm,80mm',
        ]);

        $validated['is_default'] = $request->boolean('is_default');
        $printerSetting->update($validated);

        return redirect()->route('admin.settings.index')->with('success', 'Printer berhasil diupdate!');
    }

    public function destroy(PrinterSetting $printerSetting)
    {
        $printerSetting->delete();
        return redirect()->route('admin.settings.index')->with('success', 'Printer berhasil dihapus!');
    }
}
