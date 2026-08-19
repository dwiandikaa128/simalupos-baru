<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use App\Models\AppSetting;
use App\Models\ActivityLog;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::active()->orderBy('sort_order')->get();
        $products = Product::with('variants', 'category', 'recipes.ingredient')
            ->available()
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($product) => $this->decorateStockAvailability($product))
            ->values();

        $heldOrders = Order::where('status', 'held')
            ->where('user_id', auth()->id())
            ->with('items')
            ->get();

        $taxRate = AppSetting::getTaxRate();
        $serviceChargeRate = AppSetting::getServiceChargeRate();
        $receiptSettings = [
            'shop_name' => AppSetting::get('shop_name', 'SimaluCoffee'),
            'shop_address' => AppSetting::get('shop_address', ''),
            'shop_phone' => AppSetting::get('shop_phone', ''),
            'receipt_header' => AppSetting::get('receipt_header', ''),
            'receipt_footer' => AppSetting::get('receipt_footer', 'Terima kasih!'),
        ];

        $taxOnlyForDebit = AppSetting::get('tax_only_for_debit', 'false') === 'true';
        $debitBcaRate = (float) AppSetting::get('debit_bca_tax_rate', '0.15');
        $debitOtherRate = (float) AppSetting::get('debit_other_tax_rate', '1.0');
        $creditBcaRate = (float) AppSetting::get('credit_bca_tax_rate', '1.5');
        $creditOtherRate = (float) AppSetting::get('credit_other_tax_rate', '2.0');

        return view('pos.index', compact(
            'categories', 'products', 'heldOrders', 'taxRate', 'serviceChargeRate', 'receiptSettings',
            'taxOnlyForDebit', 'debitBcaRate', 'debitOtherRate', 'creditBcaRate', 'creditOtherRate'
        ));
    }

    public function products(Request $request)
    {
        $query = Product::with('variants', 'category', 'recipes.ingredient')->available();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('sort_order')
            ->get()
            ->map(fn ($product) => $this->decorateStockAvailability($product))
            ->values();

        return response()->json($products);
    }

    private function decorateStockAvailability(Product $product): Product
    {
        $unavailableIngredients = $product->unavailableRecipeIngredients();

        $product->setAttribute('has_enough_stock', $unavailableIngredients->isEmpty());
        $product->setAttribute(
            'stock_unavailable_message',
            $unavailableIngredients->isEmpty()
                ? null
                : 'Stok kurang: '.$unavailableIngredients
                    ->map(fn ($recipe) => $recipe->ingredient->name)
                    ->unique()
                    ->join(', ')
        );

        return $product;
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.notes' => 'nullable|string',
            'customer_name' => 'nullable|string|max:100',
            'table_number' => 'nullable|string|max:20',
            'order_type' => 'required|in:dine_in,takeaway,online',
            'voucher_code' => 'nullable|string',
            'manual_discount_percent' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'is_held' => 'nullable|boolean',
            'held_order_id' => 'nullable|exists:orders,id',
            'payment_method' => 'nullable|string|in:cash,qris,debit,credit,transfer,ojol,simalu_membership',
            'payment_option' => 'nullable|string|max:50',
            'customer_id' => 'nullable|exists:customers,id',
            'save_change_to_membership' => 'nullable|boolean',
        ]);

        if ($request->filled('held_order_id')) {
            $oldOrder = Order::find($request->held_order_id);
            if ($oldOrder && $oldOrder->status == 'held') {
                $oldOrder->items()->delete();
                $oldOrder->delete();
            }
        }

        $subtotal = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $variant = isset($item['variant_id']) ? $product->variants()->find($item['variant_id']) : null;

            $unitPrice = $product->getEffectivePrice($variant);
            $itemSubtotal = $unitPrice * $item['quantity'];
            $subtotal += $itemSubtotal;

            $orderItems[] = [
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'product_name' => $product->name,
                'variant_name' => $variant?->name,
                'unit_price' => $unitPrice,
                'quantity' => $item['quantity'],
                'subtotal' => $itemSubtotal,
                'notes' => $item['notes'] ?? null,
            ];
        }

        // Discount Calculation
        $discountAmount = 0;
        $voucherCode = null;
        $usedVoucher = null;
        
        if ($request->filled('manual_discount_percent') && $request->manual_discount_percent > 0) {
            $discountAmount = $subtotal * ($request->manual_discount_percent / 100);
        } elseif ($request->filled('voucher_code')) {
            $voucher = Voucher::where('code', strtoupper($request->voucher_code))->first();
            if ($voucher) {
                $validation = $voucher->isValid($subtotal);
                if ($validation['valid']) {
                    $discountAmount = $voucher->calculateDiscount($subtotal);
                    $voucherCode = $voucher->code;
                    $voucher->increment('used_count');
                    $usedVoucher = $voucher;
                }
            }
        }

        // Tax calculation
        $serviceChargeRate = AppSetting::getServiceChargeRate();
        $serviceChargeAmount = ($subtotal - $discountAmount) * ($serviceChargeRate / 100);

        $taxRate = 0;
        if (($request->payment_method === 'debit' || $request->payment_method === 'credit') && $request->filled('payment_option')) {
            $option = $request->payment_option;
            if ($option === 'debit_bca') {
                $taxRate = (float) AppSetting::get('debit_bca_tax_rate', '0.15');
            } elseif ($option === 'debit_other') {
                $taxRate = (float) AppSetting::get('debit_other_tax_rate', '1.0');
            } elseif ($option === 'credit_bca') {
                $taxRate = (float) AppSetting::get('credit_bca_tax_rate', '1.5');
            } elseif ($option === 'credit_other') {
                $taxRate = (float) AppSetting::get('credit_other_tax_rate', '2.0');
            }
        } else {
            $taxRate = AppSetting::getTaxRate();
            $taxOnlyForDebit = AppSetting::get('tax_only_for_debit', 'false') === 'true';
            if ($taxOnlyForDebit && $request->payment_method !== 'debit' && $request->payment_method !== 'credit') {
                $taxRate = 0;
            }
        }

        $taxAmount = ($subtotal - $discountAmount + $serviceChargeAmount) * ($taxRate / 100);
        $totalAmount = $subtotal - $discountAmount + $serviceChargeAmount + $taxAmount;

        $customerName = $request->customer_name;
        if ($request->filled('customer_id')) {
            $cust = \App\Models\Customer::find($request->customer_id);
            if ($cust) {
                $customerName = $cust->name;
            }
        }

        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => auth()->id(),
            'customer_id' => $request->customer_id,
            'customer_name' => $customerName,
            'table_number' => $request->table_number,
            'order_type' => $request->order_type,
            'status' => $request->is_held ? 'held' : 'pending',
            'payment_method' => $request->payment_method,
            'payment_option' => $request->payment_option,
            'payment_status' => 'unpaid',
            'held_at' => $request->is_held ? now() : null,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'service_charge_amount' => $serviceChargeAmount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'notes' => $request->notes,
            'voucher_code' => $voucherCode,
        ]);

        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        // Mark voucher as redeemed with order reference
        if ($usedVoucher) {
            $usedVoucher->markAsRedeemed($order->id);
        }

        ActivityLog::log('create_order', "Membuat pesanan {$order->order_number}", $order);

        return response()->json([
            'success' => true,
            'order' => $order->load('items'),
            'message' => 'Pesanan berhasil dibuat!',
        ]);
    }

    public function holdOrder(Order $order)
    {
        $order->update([
            'status' => 'held',
            'held_at' => now(),
        ]);

        ActivityLog::log('hold_order', "Menahan pesanan {$order->order_number}", $order);

        return response()->json(['success' => true, 'message' => 'Pesanan ditahan']);
    }

    public function heldOrders()
    {
        $orders = Order::where('status', 'held')
            ->where('user_id', auth()->id())
            ->with('items')
            ->latest('held_at')
            ->get();

        return response()->json($orders);
    }

    public function resumeOrder(Order $order)
    {
        $order->update([
            'status' => 'pending',
            'held_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'order' => $order->load('items'),
            'message' => 'Pesanan dilanjutkan',
        ]);
    }

    public function processPayment(Request $request, Order $order, InventoryService $inventoryService)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,qris,debit,credit,transfer,ojol,simalu_membership',
            'payment_option' => 'nullable|string|max:50',
            'amount_paid' => 'nullable|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'save_change_to_membership' => 'nullable|boolean',
        ]);

        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => true,
                'order' => $order->load('items'),
                'message' => 'Pesanan sudah dibayar.',
            ]);
        }

        $customerId = $request->customer_id ?? $order->customer_id;
        $amountPaid = $request->amount_paid ?? $order->total_amount;
        $changeAmount = max(0, $amountPaid - $order->total_amount);
        $paidByMembership = 0;
        $changeToMembership = 0;

        try {
            DB::transaction(function () use ($request, $order, $customerId, $amountPaid, $changeAmount, &$paidByMembership, &$changeToMembership, $inventoryService) {
                $membershipService = app(\App\Services\CustomerMembershipService::class);

                // Handle payment via Simalu Membership
                if ($request->payment_method === 'simalu_membership') {
                    if (!$customerId) {
                        throw new \Exception('Silakan pilih member terlebih dahulu untuk pembayaran Simalu Membership.');
                    }

                    $paidByMembership = $order->total_amount;
                    $membershipService->payWithBalance($customerId, $paidByMembership, $order->id);
                    
                    $amountPaid = $order->total_amount;
                    $changeAmount = 0;
                }

                // Handle save change to membership if requested
                if ($request->save_change_to_membership && $changeAmount > 0) {
                    if (!$customerId) {
                        throw new \Exception('Silakan pilih member untuk menyimpan uang kembalian ke saldo.');
                    }

                    $changeToMembership = $changeAmount;
                    $membershipService->saveChangeToBalance($customerId, $changeToMembership, $order->id);
                    $changeAmount = 0; // Reset change to 0 since it went into membership balance
                }

                $order->update([
                    'customer_id' => $customerId,
                    'payment_method' => $request->payment_method,
                    'payment_option' => $request->payment_option,
                    'payment_status' => 'paid',
                    'status' => 'processing',
                    'amount_paid' => $amountPaid,
                    'change_amount' => $changeAmount,
                    'paid_by_membership' => $paidByMembership,
                    'change_to_membership' => $changeToMembership,
                    'paid_at' => now(),
                ]);

                $inventoryService->deductForPaidOrder($order);

                // Update shift stats
                $shift = auth()->user()->activeShift();
                if ($shift) {
                    $shift->increment('total_sales', $order->total_amount);
                    $shift->increment('total_transactions');
                }
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?: 'Validasi gagal.',
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Payment processing failed: ' . $e->getMessage(), ['order_id' => $order->id]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        ActivityLog::log('process_payment', "Pembayaran pesanan {$order->order_number} via {$request->payment_method}", $order);

        return response()->json([
            'success' => true,
            'order' => $order->fresh()->load('items', 'customer'),
            'message' => 'Pembayaran berhasil!',
        ]);
    }

    public function searchCustomers(Request $request)
    {
        $search = $request->query('query', '');
        $customers = \App\Models\Customer::where('status', 'active')
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();

        return response()->json($customers);
    }

    public function storeCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:customers,phone',
        ]);

        $phone = \App\Models\Customer::formatPhoneNumber($request->phone);

        $customer = \App\Models\Customer::create([
            'name' => $request->name,
            'phone' => $phone,
            'balance' => 0,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'customer' => $customer,
            'message' => 'Member baru berhasil ditambahkan!',
        ]);
    }


    public function cancelOrder(Request $request, Order $order, InventoryService $inventoryService)
    {
        $voidPin = \App\Models\AppSetting::get('void_pin');
        
        if (empty($voidPin)) {
            return response()->json(['success' => false, 'message' => 'Fitur Void belum diaktifkan oleh Admin (PIN belum diatur).'], 403);
        }
        
        if ($request->pin !== $voidPin) {
            return response()->json(['success' => false, 'message' => 'PIN/Password Void salah.'], 403);
        }

        if ($order->payment_status === 'paid') {
            $inventoryService->restoreForCancelledOrder($order);
            
            // Decrease shift stats if this order was paid and counted in the current active shift
            $shift = auth()->user()->activeShift();
            if ($shift) {
                // Determine if we should decrease (we just assume yes since it's the current shift balance)
                $shift->decrement('total_sales', $order->total_amount);
                $shift->decrement('total_transactions');
            }
        }

        $order->update(['status' => 'cancelled']);
        ActivityLog::log('cancel_order', "Membatalkan pesanan {$order->order_number} (Void)", $order);

        return response()->json(['success' => true, 'message' => 'Pesanan berhasil dibatalkan (Void)']);
    }

    public function validateVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $voucher = Voucher::where('code', strtoupper($request->code))->first();

        if (!$voucher) {
            return response()->json(['valid' => false, 'message' => 'Kode voucher tidak ditemukan', 'discount_amount' => 0]);
        }

        $validation = $voucher->isValid($request->subtotal);

        if (!$validation['valid']) {
            return response()->json(['valid' => false, 'message' => $validation['message'], 'discount_amount' => 0]);
        }

        $discount = $voucher->calculateDiscount($request->subtotal);

        return response()->json([
            'valid' => true,
            'message' => $validation['message'],
            'discount_amount' => $discount,
            'voucher_name' => $voucher->name,
        ]);
    }
}
