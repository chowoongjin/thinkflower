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
            : Carbon::today()->subDays(15)->format('Y-m-d');

        $dateTo = $request->filled('date_to')
            ? $request->input('date_to')
            : Carbon::today()->addDays(15)->format('Y-m-d');

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

    public function assignReceiver(Request $request, Order $order)
    {
        $validated = $request->validate([
            'receiver_shop_id' => ['required', 'integer', 'exists:shops,id'],
        ]);

        $adminUser = $request->user();
        $fromStatus = $order->current_status;

        $order->update([
            'receiver_shop_id' => $validated['receiver_shop_id'],
            'assigned_by_admin_user_id' => $adminUser->id,
            'brokerage_type' => 'assigned',
            'current_status' => 'submitted',
            'accepted_at' => null,
            'accepted_by_type' => null,
            'delivered_at' => null,
            'receiver_name' => null,
            'receiver_relation' => null,
        ]);

        DB::table('order_histories')->insert([
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'history_type' => 'updated',
            'message' => '<strong>본부 수발주사업부</strong> 에서 수주사 중개 시작',
            'processed_at' => now(),
            'actor_user_id' => $adminUser->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('order_status_logs')->insert([
            'order_id' => $order->id,
            'from_status' => $fromStatus,
            'to_status' => 'submitted',
            'changed_by_user_id' => $adminUser->id,
            'memo' => '본부 수주사 지정',
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
    public function accept(Request $request, Order $order)
    {
        $adminUser = $request->user();

        abort_unless($adminUser, 403);

        if ((int) ($order->receiver_shop_id ?? 0) === 0) {
            return response()->json([
                'message' => '수주사가 지정되지 않은 주문입니다.',
            ], 422);
        }

        if ($order->current_status === 'delivered') {
            return response()->json([
                'message' => '배송완료된 주문건 입니다.',
            ], 422);
        }

        DB::transaction(function () use ($order, $adminUser) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $fromStatus = $lockedOrder->current_status;

            if ((int) ($lockedOrder->receiver_shop_id ?? 0) === 0) {
                abort(422, '수주사가 지정되지 않은 주문입니다.');
            }

            if ($lockedOrder->current_status === 'delivered') {
                abort(422, '배송완료된 주문건 입니다.');
            }

            $lockedOrder->update([
                'brokerage_type' => 'assigned',
                'current_status' => 'accepted',
                'accepted_at' => now(),
                'accepted_by_type' => 'admin',
                'assigned_by_admin_user_id' => $adminUser->id,
            ]);

            DB::table('order_histories')->insert([
                'order_id' => $lockedOrder->id,
                'order_no' => $lockedOrder->order_no,
                'history_type' => 'accepted',
                'message' => '<strong>본부 수발주사업부</strong> 에서 주문접수 처리',
                'processed_at' => now(),
                'actor_user_id' => $adminUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('order_status_logs')->insert([
                'order_id' => $lockedOrder->id,
                'from_status' => $fromStatus,
                'to_status' => 'accepted',
                'changed_by_user_id' => $adminUser->id,
                'memo' => '본부 주문접수 처리',
                'created_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => '주문접수 처리되었습니다.',
        ]);
    }
    public function resetBrokerage(Request $request, Order $order)
    {
        $adminUser = $request->user();

        abort_unless($adminUser, 403);

        if ($order->current_status === 'delivered') {
            return response()->json([
                'message' => '배송완료된 주문건 입니다.',
            ], 422);
        }

        DB::transaction(function () use ($order, $adminUser) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $fromStatus = $lockedOrder->current_status;

            if ($lockedOrder->current_status === 'delivered') {
                abort(422, '배송완료된 주문건 입니다.');
            }

            $lockedOrder->update([
                'receiver_shop_id' => null,
                'assigned_by_admin_user_id' => null,
                'brokerage_type' => 'waiting',
                'current_status' => 'submitted',
                'accepted_at' => null,
                'accepted_by_type' => null,
                'delivered_at' => null,
                'receiver_name' => null,
                'receiver_relation' => null,
            ]);

            DB::table('order_histories')->insert([
                'order_id' => $lockedOrder->id,
                'order_no' => $lockedOrder->order_no,
                'history_type' => 'updated',
                'message' => '<strong>본부 수발주사업부</strong> 에서 수주사 재선정',
                'processed_at' => now(),
                'actor_user_id' => $adminUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('order_status_logs')->insert([
                'order_id' => $lockedOrder->id,
                'from_status' => $fromStatus,
                'to_status' => 'submitted',
                'changed_by_user_id' => $adminUser->id,
                'memo' => '수주사지정 초기화',
                'created_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => '중개대기로 변경되었습니다.',
        ]);
    }
}
