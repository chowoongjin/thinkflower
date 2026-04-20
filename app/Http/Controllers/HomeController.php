<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Banner;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $shop = $user?->shop;
        $now = now();

        $mainBanners = Banner::query()
            ->where('banner_type', 'main')
            ->whereNotNull('image_path')
            ->where('image_path', '!=', '')
            ->where(function ($query) use ($now) {
                $query->whereNull('start_at')
                    ->orWhere('start_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('end_at')
                    ->orWhere('end_at', '>=', $now);
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        $mainBanner = $mainBanners->first();

        $recentReceives = collect();
        $waitingOrderCount = 0;
        $acceptedReceiveCount = 0;
        $uncheckedReceiveCount = 0;
        $recentOrders = collect();

        if ($shop) {
            $waitingOrderCount = Order::query()
                ->where('orderer_shop_id', $shop->id)
                ->where('is_hidden', 0)
                ->where('brokerage_type', 'waiting')
                ->count();

            $acceptedReceiveCount = Order::query()
                ->where('receiver_shop_id', $shop->id)
                ->where('is_hidden', 0)
                ->where('current_status', 'accepted')
                ->count();

            $uncheckedReceiveCount = Order::query()
                ->where('receiver_shop_id', $shop->id)
                ->where('is_hidden', 0)
                ->where('current_status', 'submitted')
                ->count();

            $recentOrders = Order::query()
                ->with(['ordererShop', 'receiverShop'])
                ->withCount(['uploadedPhotos as photos_count'])
                ->where('orderer_shop_id', $shop->id)
                ->where('is_hidden', 0)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            $recentReceives = Order::query()
                ->with(['ordererShop', 'receiverShop'])
                ->withCount(['uploadedPhotos as photos_count'])
                ->where('receiver_shop_id', $shop->id)
                ->where('is_hidden', 0)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        }

        return view('pages.index', [
            'mainBanner' => $mainBanner,
            'mainBanners' => $mainBanners,
            'waitingOrderCount' => $waitingOrderCount,
            'acceptedReceiveCount' => $acceptedReceiveCount,
            'uncheckedReceiveCount' => $uncheckedReceiveCount,
            'recentOrders' => $recentOrders,
            'recentReceives' => $recentReceives,
        ]);
    }
}
