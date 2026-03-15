<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $recentReceives = collect();
        $user = auth()->user();
        $shop = $user?->shop;
        $now = now();

        $mainBanner = DB::table('banners')
            ->where('banner_type', 'main')
            ->where('is_active', 1)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')
                    ->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')
                    ->orWhere('end_at', '>=', $now);
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->first();

        $waitingOrderCount = 0;
        $acceptedReceiveCount = 0;
        $uncheckedReceiveCount = 0;
        $recentOrders = collect();

        if ($shop) {
            $waitingOrderCount = Order::query()
                ->where('orderer_shop_id', $shop->id)
                ->where('brokerage_type', 'waiting')
                ->count();

            $acceptedReceiveCount = Order::query()
                ->where('receiver_shop_id', $shop->id)
                ->where('current_status', 'accepted')
                ->count();

            $uncheckedReceiveCount = Order::query()
                ->where('receiver_shop_id', $shop->id)
                ->where('current_status', 'submitted')
                ->count();

            $recentOrders = Order::query()
                ->with(['ordererShop', 'receiverShop'])
                ->withCount('photos')
                ->where('orderer_shop_id', $shop->id)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            $recentReceives = Order::query()
                ->with(['ordererShop', 'receiverShop'])
                ->withCount('photos')
                ->where('receiver_shop_id', $shop->id)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        }

        return view('pages.index', [
            'mainBanner' => $mainBanner,
            'waitingOrderCount' => $waitingOrderCount,
            'acceptedReceiveCount' => $acceptedReceiveCount,
            'uncheckedReceiveCount' => $uncheckedReceiveCount,
            'recentOrders' => $recentOrders,
            'recentReceives' => $recentReceives,
        ]);
    }
}
