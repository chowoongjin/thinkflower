<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shop;
use App\Models\OrderPhoto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AllOrderListController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->filled('date_from')
            ? $request->input('date_from')
            : Carbon::today()->startOfMonth()->format('Y-m-d');

        $dateTo = $request->filled('date_to')
            ? $request->input('date_to')
            : Carbon::today()->format('Y-m-d');

        $productType = trim((string) $request->input('product_type', '전체상품'));
        $statusType = trim((string) $request->input('status_type', '전체상태'));
        $orderNo = trim((string) $request->input('order_no', ''));
        $recipientName = trim((string) $request->input('recipient_name', ''));
        $deliveryAddr = trim((string) $request->input('delivery_addr', ''));
        $rangePreset = trim((string) $request->input('range_preset', ''));

        $query = Order::query()
            ->with(['ordererShop', 'receiverShop'])
            ->withCount('photos')
            ->whereDate('delivery_date', '>=', $dateFrom)
            ->whereDate('delivery_date', '<=', $dateTo);

        if ($productType !== '' && $productType !== '전체상품') {
            if ($productType === '근조화환') {
                $query->where(function ($q) {
                    $q->where('product_name', 'like', '근조3단%')
                        ->orWhere('product_name', 'like', '근조화환%');
                });
            } elseif ($productType === '축하화환') {
                $query->where(function ($q) {
                    $q->where('product_name', 'like', '축하3단%')
                        ->orWhere('product_name', 'like', '축하화환%');
                });
            } else {
                $query->where('product_name', 'like', '%' . $productType . '%');
            }
        }

        if ($statusType !== '' && $statusType !== '전체상태') {
            if ($statusType === '중개필요') {
                $query->where('brokerage_type', 'waiting');
            } elseif ($statusType === '주문접수') {
                $query->where('current_status', 'accepted');
            } elseif ($statusType === '배송완료') {
                $query->where('current_status', 'delivered');
            } elseif ($statusType === '주문취소') {
                $query->where('current_status', 'cancelled');
            } elseif ($statusType === '삭제처리') {
                $query->onlyTrashed();
            }
        }

        if ($orderNo !== '') {
            $query->where('order_no', 'like', '%' . $orderNo . '%');
        }

        if ($recipientName !== '') {
            $query->where('recipient_name', 'like', '%' . $recipientName . '%');
        }

        if ($deliveryAddr !== '') {
            $query->where('delivery_addr1', 'like', '%' . $deliveryAddr . '%');
        }

        $orders = (clone $query)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $totalOrderAmount = (clone $query)->sum('original_amount');
        $totalPaymentAmount = (clone $query)
            ->where('current_status', 'delivered')
            ->sum('order_amount');

        $data = compact(
            'orders',
            'dateFrom',
            'dateTo',
            'productType',
            'statusType',
            'orderNo',
            'recipientName',
            'deliveryAddr',
            'rangePreset',
            'totalOrderAmount',
            'totalPaymentAmount'
        );

        if ($request->ajax()) {
            return response()->json([
                'table_html' => view('admin.partials.all-order-list-table', $data)->render(),
                'total_order_amount' => number_format($totalOrderAmount),
                'total_payment_amount' => number_format($totalPaymentAmount),
            ]);
        }

        return view('admin.all-order-list', $data);
    }
    public function ordererShop()
    {
        return $this->belongsTo(Shop::class, 'orderer_shop_id');
    }

    public function receiverShop()
    {
        return $this->belongsTo(Shop::class, 'receiver_shop_id');
    }

    public function photos()
    {
        return $this->hasMany(OrderPhoto::class, 'order_id');
    }

    public function popup(Order $order)
    {
        $order->load([
            'ordererShop',
            'receiverShop',
        ]);

        return view('admin.all-order-popup', [
            'order' => $order,
        ]);
    }

    public function historyModal(Order $order)
    {
        $histories = $order->histories()
            ->orderBy('processed_at')
            ->orderBy('id')
            ->get();

        return view('admin.partials.all-order-history-modal', [
            'order' => $order,
            'histories' => $histories,
        ]);
    }

    public function photoPopup(Order $order)
    {
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
