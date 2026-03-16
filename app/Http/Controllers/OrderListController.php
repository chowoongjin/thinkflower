<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class OrderListController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $dateFrom = $request->filled('date_from')
            ? $request->date_from
            : Carbon::today()->subDays(15)->format('Y-m-d');

        $dateTo = $request->filled('date_to')
            ? $request->date_to
            : Carbon::today()->addDays(15)->format('Y-m-d');

        $query = Order::query()
            ->with(['ordererShop', 'receiverShop'])
            ->withCount('photos')
            ->where('created_user_id', $user->id)
            ->whereDate('delivery_date', '>=', $dateFrom)
            ->whereDate('delivery_date', '<=', $dateTo);

        if ($request->filled('product_name')) {
            $productName = trim($request->product_name);

            if ($productName === '근조화환') {
                $query->where(function ($q) {
                    $q->where('product_name', 'like', '근조3단%')
                        ->orWhere('product_name', 'like', '근조화환%');
                });
            } elseif ($productName === '축하화환') {
                $query->where(function ($q) {
                    $q->where('product_name', 'like', '축하3단%')
                        ->orWhere('product_name', 'like', '축하화환%');
                });
            } else {
                $query->where('product_name', 'like', '%' . $productName . '%');
            }
        }

        if ($request->filled('order_no')) {
            $query->where('order_no', 'like', '%' . trim($request->order_no) . '%');
        }

        if ($request->filled('delivery_addr1')) {
            $query->where('delivery_addr1', 'like', '%' . trim($request->delivery_addr1) . '%');
        }

        if ($request->filled('recipient_name')) {
            $query->where('recipient_name', 'like', '%' . trim($request->recipient_name) . '%');
        }

        $orders = (clone $query)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $summaryCount = (clone $query)->count();
        $summaryAmount = (clone $query)->sum('payment_amount');

        $data = compact(
            'orders',
            'summaryCount',
            'summaryAmount',
            'dateFrom',
            'dateTo'
        );

        if ($request->ajax()) {
            return view('pages.partials.order-list-table', $data);
        }

        return view('pages.order-list', $data);
    }
    public function popup(Request $request, Order $order)
    {
        abort_unless($order->created_user_id === $request->user()->id, 403);

        $order->load([
            'ordererShop',
            'receiverShop',
        ]);

        return view('pages.order-popup', [
            'order' => $order,
            'title' => '주문정보',
        ]);
    }
    public function historyModal(Request $request, Order $order)
    {
        abort_unless($order->created_user_id === $request->user()->id, 403);

        $histories = $order->histories()
            ->orderBy('processed_at')
            ->orderBy('id')
            ->get();

        return view('pages.partials.order-history-modal', [
            'order' => $order,
            'histories' => $histories,
        ]);
    }
    public function photoPopup(Request $request, Order $order)
    {
        abort_unless($order->created_user_id === $request->user()->id, 403);

        $photos = DB::table('order_photos')
            ->where('order_id', $order->id)
            ->orderBy('sort_order')
            ->get();

        return view('pages.order-photo-popup', [
            'order' => $order,
            'photos' => $photos,
        ]);
    }
}
