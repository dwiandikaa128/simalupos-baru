<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BaristaReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();

        $activeShift = Shift::where('user_id', $user->id)->where('status', 'active')->first();

        $shifts = Shift::where('user_id', $user->id)->latest('started_at')->take(10)->get();

        $historyDate = $request->get('date');
        $isHistory = $historyDate && $historyDate !== $today->format('Y-m-d');

        if ($isHistory) {
            $filterDate = Carbon::parse($historyDate);

            $viewOrders = Order::where('user_id', $user->id)
                ->paid()
                ->whereDate('paid_at', $filterDate)
                ->latest('paid_at')
                ->get();

            $viewSales = $viewOrders->sum('total_amount');
            $viewTransactions = $viewOrders->count();
            $viewLabel = $filterDate->format('d/m/Y');
        } else {
            if ($activeShift) {
                $viewOrders = Order::where('user_id', $user->id)
                    ->paid()
                    ->where('paid_at', '>=', $activeShift->started_at)
                    ->latest('paid_at')
                    ->get();

                $viewSales = $viewOrders->sum('total_amount');
                $viewTransactions = $viewOrders->count();
            } else {
                $viewOrders = collect();
                $viewSales = 0;
                $viewTransactions = 0;
            }
            $viewLabel = 'Shift Ini';
        }

        return view('pos.my-reports', compact(
            'activeShift', 'shifts', 'isHistory', 'historyDate',
            'viewOrders', 'viewSales', 'viewTransactions', 'viewLabel'
        ));
    }
}
