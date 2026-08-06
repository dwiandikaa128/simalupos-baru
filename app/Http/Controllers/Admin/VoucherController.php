<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::with('order')->latest()->get();

        // Group by batch
        $batchGroups = $vouchers->whereNotNull('batch_group')
            ->groupBy('batch_group')
            ->map(function ($group) {
                return [
                    'total' => $group->count(),
                    'redeemed' => $group->whereNotNull('redeemed_at')->count(),
                    'first' => $group->last(), // oldest in group
                ];
            });

        return view('admin.vouchers.index', compact('vouchers', 'batchGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:vouchers|max:50',
            'name' => 'required|max:100',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['code'] = strtoupper($validated['code']);
        Voucher::create($validated);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil ditambahkan!');
    }

    public function generateBatch(Request $request)
    {
        $validated = $request->validate([
            'prefix' => 'required|string|max:30',
            'count' => 'required|integer|min:1|max:100',
            'name' => 'required|string|max:100',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
        ]);

        $prefix = strtoupper($validated['prefix']);
        $count = $validated['count'];
        $batchGroup = $prefix . '-' . now()->format('YmdHis');

        // Find the highest existing number with this prefix
        $existingMax = Voucher::where('code', 'like', $prefix . '-%')
            ->get()
            ->map(function ($v) use ($prefix) {
                $suffix = str_replace($prefix . '-', '', $v->code);
                return is_numeric($suffix) ? (int) $suffix : 0;
            })
            ->max() ?? 0;

        $created = 0;
        $digits = strlen((string) ($existingMax + $count));
        $digits = max($digits, 3); // minimum 3 digits

        for ($i = 1; $i <= $count; $i++) {
            $number = $existingMax + $i;
            $code = $prefix . '-' . str_pad($number, $digits, '0', STR_PAD_LEFT);

            // Skip if code already exists
            if (Voucher::where('code', $code)->exists()) {
                continue;
            }

            Voucher::create([
                'code' => $code,
                'name' => $validated['name'],
                'type' => $validated['type'],
                'value' => $validated['value'],
                'min_purchase' => $validated['min_purchase'] ?? 0,
                'max_discount' => $validated['max_discount'],
                'usage_limit' => 1, // Each batch code is single-use
                'is_active' => true,
                'valid_from' => $validated['valid_from'],
                'valid_until' => $validated['valid_until'],
                'batch_group' => $batchGroup,
            ]);
            $created++;
        }

        return redirect()->route('admin.vouchers.index')
            ->with('success', "{$created} kode voucher berhasil di-generate! ({$prefix}-" . str_pad($existingMax + 1, $digits, '0', STR_PAD_LEFT) . " s/d {$prefix}-" . str_pad($existingMax + $count, $digits, '0', STR_PAD_LEFT) . ")");
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code' => 'required|max:50|unique:vouchers,code,' . $voucher->id,
            'name' => 'required|max:100',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['code'] = strtoupper($validated['code']);
        $voucher->update($validated);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil diupdate!');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil dihapus!');
    }

    public function destroyBatch(Request $request)
    {
        $request->validate(['batch_group' => 'required|string']);
        
        $count = Voucher::where('batch_group', $request->batch_group)->delete();
        
        return redirect()->route('admin.vouchers.index')
            ->with('success', "{$count} kode voucher batch berhasil dihapus!");
    }
}
