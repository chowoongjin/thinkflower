<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class HomeController extends Controller
{
    public function index()
    {
        $now = now();
        $today = $now->toDateString();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $dashboardMonthLabel = $now->format('Y년 m월');

        $mediationBaseQuery = Order::query()
            ->with(['ordererShop', 'receiverShop'])
            ->where(function ($query) {
                $query->whereNull('receiver_shop_id')
                    ->orWhere('receiver_shop_id', 0);
            });

        $mediationPendingAmount = (int) (clone $mediationBaseQuery)->sum('order_amount');

        $monthlyReceivedAmount = (int) Order::query()
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('payment_amount');

        $monthlyUsageFeeAmount = (int) Order::query()
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('brokerage_fee');

        $mediationDashboardCount = (clone $mediationBaseQuery)->count();

        $mediationDashboardOrders = (clone $mediationBaseQuery)
            ->orderByRaw("
                CASE
                    WHEN delivery_date > CURDATE() THEN 1
                    WHEN delivery_date = CURDATE() THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('delivery_date')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $todayCheckBaseQuery = Order::query()
            ->with(['ordererShop', 'receiverShop'])
            ->withCount('photos')
            ->where(function ($query) {
                $query->whereNull('current_status')
                    ->orWhere('current_status', '!=', 'delivered');
            })
            ->where(function ($query) use ($now) {
                $query->whereDate('delivery_date', '<', $now->toDateString())
                    ->orWhere(function ($subQuery) use ($now) {
                        $subQuery->whereDate('delivery_date', $now->toDateString());

                        if ($now->hour !== null && $now->minute !== null) {
                            $subQuery->where(function ($timeQuery) use ($now) {
                                $timeQuery->whereNull('delivery_hour')
                                    ->orWhereNull('delivery_minute')
                                    ->orWhereRaw('(COALESCE(delivery_hour, 0) * 60 + COALESCE(delivery_minute, 0)) <= ?', [
                                        ((int) $now->hour * 60) + (int) $now->minute,
                                    ]);
                            });
                        }
                    });
            });

        $todayCheckCount = (clone $todayCheckBaseQuery)->count();

        $todayCheckOrders = (clone $todayCheckBaseQuery)
            ->orderByDesc('delivery_date')
            ->orderByDesc('delivery_hour')
            ->orderByDesc('delivery_minute')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $allOrderDashboardQuery = Order::query()
            ->with(['ordererShop', 'receiverShop'])
            ->withCount('photos')
            ->where(function ($query) {
                $query->whereNull('current_status')
                    ->orWhere('current_status', '!=', 'delivered');
            });

        $allOrderDashboardOrders = (clone $allOrderDashboardQuery)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('admin.index', [
            'dashboardMonthLabel' => $dashboardMonthLabel,
            'mediationPendingAmount' => $mediationPendingAmount,
            'monthlyReceivedAmount' => $monthlyReceivedAmount,
            'monthlyUsageFeeAmount' => $monthlyUsageFeeAmount,
            'mediationDashboardCount' => $mediationDashboardCount,
            'mediationDashboardOrders' => $mediationDashboardOrders,
            'todayCheckCount' => $todayCheckCount,
            'todayCheckOrders' => $todayCheckOrders,
            'allOrderDashboardOrders' => $allOrderDashboardOrders,
        ]);
    }
}
