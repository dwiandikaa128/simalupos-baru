<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockReport;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
    public function index()
    {
        $reports = StockReport::with('ingredient')->latest()->paginate(20);
        return view('admin.stock_reports.index', compact('reports'));
    }

    public function resolve(StockReport $stockReport)
    {
        $stockReport->update(['is_resolved' => true]);
        return back()->with('success', 'Status laporan stok telah diperbarui menjadi Selesai.');
    }
}
