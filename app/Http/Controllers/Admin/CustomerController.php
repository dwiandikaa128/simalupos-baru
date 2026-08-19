<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerMutation;
use App\Services\CustomerMembershipService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->withCount('orders')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $totalMembers = Customer::count();
        $totalBalance = Customer::sum('balance');
        $activeMembers = Customer::where('status', 'active')->count();

        return view('admin.customers.index', compact('customers', 'totalMembers', 'totalBalance', 'activeMembers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'initial_balance' => 'nullable|numeric|min:0',
        ]);

        $phone = Customer::formatPhoneNumber($validated['phone']);

        $customer = Customer::create([
            'name' => $validated['name'],
            'phone' => $phone,
            'balance' => 0,
            'status' => 'active',
        ]);

        if (!empty($validated['initial_balance']) && $validated['initial_balance'] > 0) {
            app(CustomerMembershipService::class)->topUp(
                $customer->id,
                (float) $validated['initial_balance'],
                'cash',
                'Setoran Saldo Awal Pendaftaran Member'
            );
        }

        return redirect()->route('admin.customers.show', $customer->id)
            ->with('success', "Member '{$customer->name}' berhasil terdaftar!");
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        $mutations = $customer->mutations()->with(['order', 'creator'])->paginate(20);

        return view('admin.customers.show', compact('customer', 'mutations'));
    }

    public function topUp(Request $request, $id, CustomerMembershipService $service)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $service->topUp(
                $id,
                (float) $validated['amount'],
                $validated['payment_method'],
                $validated['notes']
            );

            return back()->with('success', 'Top-up saldo berhasil diproses!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadPdf($id, Request $request)
    {
        $customer = Customer::findOrFail($id);

        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->subMonths(3)->startOfDay();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $mutations = CustomerMutation::where('customer_id', $customer->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();

        $pdf = Pdf::loadView('pdf.customer-mutations', compact('customer', 'mutations', 'startDate', 'endDate'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Laporan-Mutasi-' . preg_replace('/[^a-zA-Z0-9]/', '_', $customer->name) . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}
