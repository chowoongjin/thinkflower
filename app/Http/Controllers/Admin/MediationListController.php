<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shop;
use App\Models\OrderHistory;
use App\Services\ReceiverAssignmentNotificationDispatcher;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MediationListController extends Controller
{
    public function __construct(
        protected ReceiverAssignmentNotificationDispatcher $receiverNotificationDispatcher
    ) {
    }

    public function index(Request $request)
    {
        $rangePreset = $request->string('range_preset')->value() ?: 'thisMonth';
        $dateFrom = $request->string('date_from')->value();
        $dateTo = $request->string('date_to')->value();

        if (!$dateFrom || !$dateTo) {
            [$dateFrom, $dateTo] = $this->resolvePresetRange($rangePreset);
        }

        $productType = $request->string('product_type')->value() ?: '전체상품';
        $orderNo = trim((string) $request->input('order_no', ''));
        $recipientName = trim((string) $request->input('recipient_name', ''));
        $deliveryAddr = trim((string) $request->input('delivery_addr', ''));

        $query = Order::query()
            ->with(['ordererShop', 'receiverShop'])
            ->where('is_hidden', 0)
            ->where(function ($q) {
                $q->whereNull('receiver_shop_id')
                    ->orWhere('receiver_shop_id', 0);
            })
            ->whereNotIn('current_status', ['cancelled', 'deleted']);

        if ($dateFrom) {
            $query->whereDate('delivery_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('delivery_date', '<=', $dateTo);
        }

        if ($productType && $productType !== '전체상품') {
            $query->where('product_name', $productType);
        }

        if ($orderNo !== '') {
            $query->where('order_no', 'like', '%' . $orderNo . '%');
        }

        if ($recipientName !== '') {
            $query->where('recipient_name', 'like', '%' . $recipientName . '%');
        }

        if ($deliveryAddr !== '') {
            $query->where(function ($q) use ($deliveryAddr) {
                $q->where('delivery_addr1', 'like', '%' . $deliveryAddr . '%')
                    ->orWhere('delivery_addr2', 'like', '%' . $deliveryAddr . '%');
            });
        }

        $orders = $query
            ->orderByRaw("
                CASE
                    WHEN delivery_date > CURDATE() THEN 1
                    WHEN delivery_date = CURDATE() THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('delivery_date')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->appends($request->query());

        $viewData = [
            'orders' => $orders,
            'rangePreset' => $rangePreset,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'productType' => $productType,
            'orderNo' => $orderNo,
            'recipientName' => $recipientName,
            'deliveryAddr' => $deliveryAddr,
        ];

        if ($request->ajax()) {
            return response()->json([
                'table_html' => view('admin.partials.mediation-list-table', $viewData)->render(),
            ]);
        }

        return view('admin.mediation-list', $viewData);
    }

    public function popup(Order $order)
    {
        abort_if((int) $order->is_hidden === 1, 404);
        return view('admin.all-order-popup', compact('order'));
    }

    protected function resolvePresetRange(string $preset): array
    {
        $today = Carbon::today();

        return match ($preset) {
            'lastMonth' => [
                $today->copy()->subMonthNoOverflow()->startOfMonth()->format('Y-m-d'),
                $today->copy()->subMonthNoOverflow()->endOfMonth()->format('Y-m-d'),
            ],
            'today' => [
                $today->format('Y-m-d'),
                $today->format('Y-m-d'),
            ],
            'tomorrow' => [
                $today->copy()->addDay()->format('Y-m-d'),
                $today->copy()->addDay()->format('Y-m-d'),
            ],
            'yesterday' => [
                $today->copy()->subDay()->format('Y-m-d'),
                $today->copy()->subDay()->format('Y-m-d'),
            ],
            default => [
                $today->copy()->startOfMonth()->format('Y-m-d'),
                $today->copy()->endOfMonth()->format('Y-m-d'),
            ],
        };
    }

    public function assignReceiver(Request $request, Order $order)
    {
        if ($order->current_status === 'delivered') {
            return response()->json([
                'message' => '배송완료된 주문건 입니다.',
            ], 422);
        }

        $data = $request->validate([
            'receiver_shop_id' => ['required', 'integer', 'exists:shops,id'],
        ]);

        $receiverShopId = (int) $data['receiver_shop_id'];

        DB::transaction(function () use ($order, $data, $request) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->current_status === 'delivered') {
                throw new HttpResponseException(response()->json([
                    'message' => '배송완료된 주문건 입니다.',
                ], 422));
            }

            $receiverShop = Shop::findOrFail((int) $data['receiver_shop_id']);

            $fromStatus = $lockedOrder->current_status;

            $lockedOrder->receiver_shop_id = $receiverShop->id;
            $lockedOrder->brokerage_type = 'assigned';
            $lockedOrder->current_status = $lockedOrder->current_status ?: 'submitted';
            $lockedOrder->save();

            // 필요하면 히스토리도 같이 남김
            if (class_exists(OrderHistory::class)) {
                OrderHistory::create([
                    'order_id' => $lockedOrder->id,
                    'order_no' => $lockedOrder->order_no,
                    'history_type' => 'updated',
                    'message' => '<strong>본부 수발주사업부</strong> 에서 수주사 중개 시작',
                    'processed_at' => now(),
                    'actor_user_id' => optional($request->user())->id,
                ]);
            }

            DB::table('order_status_logs')->insert([
                'order_id' => $lockedOrder->id,
                'from_status' => $fromStatus,
                'to_status' => $lockedOrder->current_status,
                'changed_by_user_id' => optional($request->user())->id,
                'memo' => '본부 수발주사업부 에서 수주사 중개 시작',
                'created_at' => now(),
            ]);
        });

        $this->receiverNotificationDispatcher->dispatch(
            $order->fresh(),
            $receiverShopId,
            optional($request->user())->id
        );

        return response()->json([
            'success' => true,
            'message' => '수주사가 선택되었습니다.',
        ]);
    }
}
