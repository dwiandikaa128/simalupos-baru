<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\StockReport;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'reporter_name' => 'required|string|max:255',
            'ingredient_id' => 'nullable|exists:ingredients,id',
            'item_name' => 'required_without:ingredient_id|nullable|string|max:255',
            'status' => 'required|in:mau_habis,sudah_habis',
            'notes' => 'nullable|string',
        ]);

        $ingredient = $request->filled('ingredient_id')
            ? Ingredient::find($request->ingredient_id)
            : null;

        if ($ingredient) {
            $openReport = StockReport::where('ingredient_id', $ingredient->id)
                ->where('is_resolved', false)
                ->first();

            if ($openReport) {
                return back()->with('success', 'Bahan ini sudah punya laporan stok yang masih terbuka.');
            }
        }

        StockReport::create([
            'ingredient_id' => $ingredient?->id,
            'reporter_name' => $request->reporter_name,
            'item_name' => $ingredient?->name ?? $request->item_name,
            'status' => $request->status,
            'notes' => $request->notes,
            'source' => 'manual',
            'is_resolved' => false,
        ]);

        return back()->with('success', 'Laporan stok berhasil dikirim ke Admin!');
    }
}
