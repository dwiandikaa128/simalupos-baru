<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shift;
use App\Models\AppSetting;

class ReceiptController extends Controller
{
    public function show(Order $order)
    {
        $order->load('items', 'user');

        // Resolve employee name from shift
        $shift = Shift::where('user_id', $order->user_id)
            ->where('started_at', '<=', $order->created_at)
            ->where(function ($q) use ($order) {
                $q->whereNull('ended_at')->orWhere('ended_at', '>=', $order->created_at);
            })
            ->first();
        $cashierName = $shift->employee_name ?? $order->user->name ?? '-';

        $settings = [
            'shop_name' => AppSetting::get('shop_name', 'SimaluCoffee'),
            'shop_address' => AppSetting::get('shop_address', ''),
            'shop_phone' => AppSetting::get('shop_phone', ''),
            'receipt_header' => AppSetting::get('receipt_header', ''),
            'receipt_footer' => AppSetting::get('receipt_footer', 'Terima kasih!'),
        ];

        return view('pos.receipt', compact('order', 'settings', 'cashierName'));
    }

    public function print(Order $order)
    {
        $order->load('items', 'user');

        $shift = Shift::where('user_id', $order->user_id)
            ->where('started_at', '<=', $order->created_at)
            ->where(function ($q) use ($order) {
                $q->whereNull('ended_at')->orWhere('ended_at', '>=', $order->created_at);
            })
            ->first();
        $cashierName = $shift->employee_name ?? $order->user->name ?? '-';

        $settings = [
            'shop_name' => AppSetting::get('shop_name', 'SimaluCoffee'),
            'shop_address' => AppSetting::get('shop_address', ''),
            'shop_phone' => AppSetting::get('shop_phone', ''),
            'receipt_header' => AppSetting::get('receipt_header', ''),
            'receipt_footer' => AppSetting::get('receipt_footer', 'Terima kasih!'),
        ];

        return view('pos.receipt', compact('order', 'settings', 'cashierName'));
    }
}
