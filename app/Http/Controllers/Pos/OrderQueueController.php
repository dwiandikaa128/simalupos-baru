<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ActivityLog;
use Carbon\Carbon;

class OrderQueueController extends Controller
{
    public function index()
    {
        $pendingOrders = Order::with('items')
            ->where('status', 'pending')
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at')
            ->get();

        $processingOrders = Order::with('items')
            ->where('status', 'processing')
            ->whereDate('created_at', Carbon::today())
            ->orderBy('paid_at')
            ->get();

        $completedOrders = Order::with('items')
            ->where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('pos.queue', compact('pendingOrders', 'processingOrders', 'completedOrders'));
    }

    public function process(Order $order)
    {
        $order->update(['status' => 'processing']);
        return back()->with('success', "Pesanan {$order->order_number} sedang diproses");
    }

    public function complete(Order $order)
    {
        $order->update(['status' => 'completed']);
        ActivityLog::log('complete_order', "Menyelesaikan pesanan {$order->order_number}", $order);
        return back()->with('success', "Pesanan {$order->order_number} selesai!");
    }
}
