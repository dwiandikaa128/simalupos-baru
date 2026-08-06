<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperationalCost;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OperationalCostController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $categoryFilter = $request->get('category');

        $query = OperationalCost::forMonth($month);

        if ($categoryFilter && $categoryFilter !== 'all') {
            $query->where('category', $categoryFilter);
        }

        $costs = $query->orderBy('category')->orderBy('name')->get();

        // Always compute totals from ALL costs (unfiltered) for summary cards
        $allCosts = OperationalCost::forMonth($month)->get();
        $totalByCategory = $allCosts->groupBy('category')->map(fn ($items) => $items->sum('amount'));
        $grandTotal = $allCosts->sum('amount');

        // Available months for dropdown
        $availableMonths = OperationalCost::selectRaw('DISTINCT period_month')
            ->orderByDesc('period_month')
            ->pluck('period_month')
            ->toArray();

        if (!in_array($month, $availableMonths)) {
            $availableMonths[] = $month;
            rsort($availableMonths);
        }

        // Check if previous month has data for "copy" feature
        $prevMonth = Carbon::parse($month . '-01')->subMonth()->format('Y-m');
        $hasPrevMonthData = OperationalCost::forMonth($prevMonth)->exists();

        $categories = OperationalCost::CATEGORIES;

        return view('admin.operational-costs.index', compact(
            'costs', 'month', 'totalByCategory', 'grandTotal',
            'availableMonths', 'hasPrevMonthData', 'prevMonth', 'categories',
            'categoryFilter', 'allCosts'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:utilities,rent,maintenance,supplies,other',
            'amount' => 'required|numeric|min:0',
            'period_month' => 'required|string|size:7',
            'is_recurring' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_recurring'] = $request->boolean('is_recurring');

        OperationalCost::create($validated);

        return back()->with('success', 'Biaya operasional berhasil ditambahkan!');
    }

    public function update(Request $request, OperationalCost $operationalCost)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:utilities,rent,maintenance,supplies,other',
            'amount' => 'required|numeric|min:0',
            'is_recurring' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_recurring'] = $request->boolean('is_recurring');

        $operationalCost->update($validated);

        return back()->with('success', 'Biaya operasional berhasil diupdate!');
    }

    public function destroy(OperationalCost $operationalCost)
    {
        $month = $operationalCost->period_month;
        $operationalCost->delete();
        return redirect()->route('admin.operational-costs.index', ['month' => $month])
            ->with('success', 'Biaya operasional berhasil dihapus!');
    }

    public function copyFromPreviousMonth(Request $request)
    {
        $request->validate([
            'target_month' => 'required|string|size:7',
            'source_month' => 'required|string|size:7',
        ]);

        $sourceCosts = OperationalCost::forMonth($request->source_month)->recurring()->get();

        if ($sourceCosts->isEmpty()) {
            return back()->with('error', 'Tidak ada biaya recurring di bulan sebelumnya.');
        }

        $copied = 0;
        foreach ($sourceCosts as $cost) {
            // Skip if already exists in target month with same name
            $exists = OperationalCost::forMonth($request->target_month)
                ->where('name', $cost->name)
                ->exists();

            if (!$exists) {
                OperationalCost::create([
                    'name' => $cost->name,
                    'category' => $cost->category,
                    'amount' => $cost->amount,
                    'period_month' => $request->target_month,
                    'is_recurring' => true,
                    'notes' => $cost->notes,
                ]);
                $copied++;
            }
        }

        return back()->with('success', "{$copied} biaya recurring berhasil di-copy dari bulan sebelumnya!");
    }
}
