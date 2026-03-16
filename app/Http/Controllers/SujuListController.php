<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PointTransaction;
use App\Models\Shop;
use App\Models\OrderPhoto;
use App\Models\OrderHistory;
use App\Services\Cafe24FileUploadService;
use App\Services\PointService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SujuListController extends Controller
{
    protected Cafe24FileUploadService $uploadService;
    protected PointService $pointService;

    public function __construct(Cafe24FileUploadService $uploadService, PointService $pointService)
    {
        $this->uploadService = $uploadService;
        $this->pointService = $pointService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $shop = $user?->shop;

        abort_unless($shop, 403);

        $dateFrom = $request->filled('date_from')
            ? $request->date_from
            : Carbon::today()->subDays(15)->format('Y-m-d');

        $dateTo = $request->filled('date_to')
            ? $request->date_to
            : Carbon::today()->addDays(15)->format('Y-m-d');

        $productName = trim((string) $request->input('product_name', ''));
        $orderNo = trim((string) $request->input('order_no', ''));
        $deliveryAddr1 = trim((string) $request->input('delivery_addr1', ''));
        $recipientName = trim((string) $request->input('recipient_name', ''));

        $query = Order::query()
            ->with(['receiverShop', 'ordererShop'])
            ->withCount('photos')
            ->where('receiver_shop_id', $shop->id)
            ->whereDate('delivery_date', '>=', $dateFrom)
            ->whereDate('delivery_date', '<=', $dateTo);

        if ($productName !== '') {
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

        if ($orderNo !== '') {
            $query->where('order_no', 'like', '%' . $orderNo . '%');
        }

        if ($deliveryAddr1 !== '') {
            $query->where('delivery_addr1', 'like', '%' . $deliveryAddr1 . '%');
        }

        if ($recipientName !== '') {
            $query->where('recipient_name', 'like', '%' . $recipientName . '%');
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
            'dateTo',
            'productName',
            'orderNo',
            'deliveryAddr1',
            'recipientName'
        );

        if ($request->ajax()) {
            return view('pages.partials.suju-list-table', $data);
        }

        return view('pages.suju-list', $data);
    }

    public function popup(Request $request, Order $order)
    {
        $shop = $request->user()?->shop;

        abort_unless($shop, 403);
        abort_unless((int) $order->receiver_shop_id === (int) $shop->id, 403);

        $order->load([
            'ordererShop',
            'receiverShop',
        ]);

        return view('pages.suju-popup', [
            'order' => $order,
            'title' => '주문정보',
        ]);
    }

    public function historyModal(Request $request, Order $order)
    {
        $shop = $request->user()?->shop;

        abort_unless($shop, 403);
        abort_unless((int) $order->receiver_shop_id === (int) $shop->id, 403);

        $histories = $order->histories()
            ->orderBy('processed_at')
            ->orderBy('id')
            ->get();

        return view('pages.partials.order-history-modal', [
            'order' => $order,
            'histories' => $histories,
        ]);
    }

    public function accept(Request $request, Order $order)
    {
        if (in_array($order->current_status, ['accepted', 'delivered', 'cancelled'], true) || $order->receiver_shop_id === null) {
            return redirect()
                ->route('suju-list.popup', $order->order_no)
                ->with('success', '이미 처리된 주문입니다.');
        }

        $user = $request->user();
        $shop = $user?->shop;

        abort_unless($shop, 403);
        abort_unless((int) $order->receiver_shop_id === (int) $shop->id, 403);

        DB::transaction(function () use ($order, $user, $shop) {
            $order->refresh();

            $oldStatus = $order->current_status;
            $shopDisplayName = $this->makeShopDisplayName($shop);

            $order->update([
                'brokerage_type' => 'assigned',
                'current_status' => 'accepted',
                'accepted_at' => now(),
                'accepted_by_type' => 'shop',
            ]);

            DB::table('order_histories')->insert([
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'history_type' => 'accepted',
                'message' => '수주사 <strong>' . $shopDisplayName . '</strong> 에서 배송가능 여부 확인',
                'processed_at' => now(),
                'actor_user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('order_status_logs')->insert([
                'order_id' => $order->id,
                'from_status' => $oldStatus,
                'to_status' => 'accepted',
                'changed_by_user_id' => $user->id,
                'memo' => '수주사 배송가능 여부 확인',
                'created_at' => now(),
            ]);
        });

        return response()->view('pages.popup-action-result', [
            'message' => '주문이 접수되었습니다.',
            'redirectCurrentTo' => route('suju-list.popup', $order->order_no),
            'redirectParentTo' => route('suju-list'),
        ]);
    }

    public function reject(Request $request, Order $order)
    {
        if (in_array($order->current_status, ['accepted', 'delivered', 'cancelled'], true) || $order->receiver_shop_id === null) {
            return redirect()
                ->route('suju-list')
                ->with('error', '이미 처리된 주문입니다.');
        }

        $user = $request->user();
        $shop = $user?->shop;

        abort_unless($shop, 403);
        abort_unless((int) $order->receiver_shop_id === (int) $shop->id, 403);

        DB::transaction(function () use ($order, $user, $shop) {
            $order->refresh();

            $oldStatus = $order->current_status;
            $shopDisplayName = $this->makeShopDisplayName($shop);

            $order->update([
                'receiver_shop_id' => null,
                'brokerage_type' => 'waiting',
                'current_status' => 'submitted',
                'accepted_at' => null,
                'accepted_by_type' => null,
            ]);

            DB::table('order_histories')->insert([
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'history_type' => 'updated',
                'message' => '수주사 <strong>' . $shopDisplayName . '</strong> 에서 배송불가 확인',
                'processed_at' => now(),
                'actor_user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('order_status_logs')->insert([
                'order_id' => $order->id,
                'from_status' => $oldStatus,
                'to_status' => 'submitted',
                'changed_by_user_id' => $user->id,
                'memo' => '수주사 배송불가 확인',
                'created_at' => now(),
            ]);
        });

        return response()->view('pages.popup-action-result', [
            'message' => '주문이 거절되었습니다.',
            'redirectParentTo' => route('suju-list'),
            'closeWindow' => true,
        ]);
    }

    public function completePopup(Request $request, Order $order)
    {
        $shop = $request->user()?->shop;

        abort_unless($shop, 403);
        abort_unless((int) $order->receiver_shop_id === (int) $shop->id, 403);

        $order->load([
            'ordererShop',
            'receiverShop',
        ]);

        $photos = DB::table('order_photos')
            ->where('order_id', $order->id)
            ->orderBy('sort_order')
            ->get();

        $photoShop = $photos->first(function ($photo) {
            return $photo->photo_type === 'other' && (int) $photo->sort_order === 1;
        });

        $photoSite = $photos->first(function ($photo) {
            return $photo->photo_type === 'delivery_site';
        });

        $photoExtra = $photos->first(function ($photo) {
            return $photo->photo_type === 'other' && (int) $photo->sort_order === 3;
        });

        return view('pages.suju-complete-popup', [
            'order' => $order,
            'photoShop' => $photoShop,
            'photoSite' => $photoSite,
            'photoExtra' => $photoExtra,
        ]);
    }

    public function completeStore(Request $request, Order $order)
    {
        $user = $request->user();
        abort_unless($user, 403);

        $validated = $request->validate([
            'completed_date' => ['required', 'date'],
            'completed_hour' => ['required', 'integer', 'between:0,23'],
            'completed_minute' => ['required', 'in:00,10,20,30,40,50'],
            'receiver_name' => ['required', 'string', 'max:100'],
            'receiver_relation' => ['nullable', 'string', 'max:50'],
        ], [
            'completed_date.required' => '배송완료일을 입력해 주세요.',
            'completed_hour.required' => '배송완료시간을 선택해 주세요.',
            'completed_minute.required' => '배송완료분을 선택해 주세요.',
            'receiver_name.required' => '인수자를 입력해 주세요.',
        ]);

        if ($order->current_status === 'delivered') {
            throw ValidationException::withMessages([
                'receiver_name' => '이미 배송완료 처리된 주문입니다.',
            ]);
        }

        $deliveredAt = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $validated['completed_date'] . ' ' .
            str_pad((string) $validated['completed_hour'], 2, '0', STR_PAD_LEFT) . ':' .
            $validated['completed_minute'] . ':00'
        );

        DB::transaction(function () use ($order, $user, $validated, $deliveredAt) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->current_status === 'delivered') {
                throw ValidationException::withMessages([
                    'receiver_name' => '이미 배송완료 처리된 주문입니다.',
                ]);
            }

            $fromStatus = $lockedOrder->current_status;

            // brokerage_type 은 waiting / assigned / not_needed 구조이므로 배송완료 시 current_status 만 변경
            $lockedOrder->update([
                'current_status' => 'delivered',
                'delivered_at' => $deliveredAt,
                'receiver_name' => $validated['receiver_name'],
                'receiver_relation' => $validated['receiver_relation'] ?? null,
            ]);

            $receiverShop = $lockedOrder->receiver_shop_id
                ? Shop::where('id', $lockedOrder->receiver_shop_id)->lockForUpdate()->first()
                : null;

            OrderHistory::create([
                'order_id' => $lockedOrder->id,
                'order_no' => $lockedOrder->order_no,
                'history_type' => 'delivered',
                'message' => '수주사 <strong>' . $this->makeShopDisplayName($receiverShop) . '</strong> 인수정보 업로드 완료',
                'processed_at' => now(),
                'actor_user_id' => $user->id,
            ]);

            DB::table('order_status_logs')->insert([
                'order_id' => $lockedOrder->id,
                'from_status' => $fromStatus,
                'to_status' => 'delivered',
                'changed_by_user_id' => $user->id,
                'memo' => '배송완료 및 인수정보 등록',
                'created_at' => now(),
            ]);

            // 정책: 수주사는 배송완료 시 발주금액(order_amount) 만큼 적립
            $pointAmount = (int) $lockedOrder->point_earned_amount;

            if ($pointAmount <= 0) {
                $pointAmount = (int) $lockedOrder->order_amount;

                if ($pointAmount > 0) {
                    $lockedOrder->update([
                        'point_earned_amount' => $pointAmount,
                    ]);
                }
            }

            if ($receiverShop && $pointAmount > 0) {
                $alreadyCredited = DB::table('point_transactions')
                    ->where('order_id', $lockedOrder->id)
                    ->where('shop_id', $receiverShop->id)
                    ->where('transaction_type', 'order_credit')
                    ->exists();

                if (!$alreadyCredited) {
                    $beforeBalance = (int) $receiverShop->current_point_balance;
                    $afterBalance = $beforeBalance + $pointAmount;

                    $receiverShop->update([
                        'current_point_balance' => $afterBalance,
                    ]);

                    DB::table('point_transactions')->insert([
                        'shop_id' => $receiverShop->id,
                        'user_id' => $user->id,
                        'order_id' => $lockedOrder->id,
                        'payment_transaction_id' => null,
                        'transaction_no' => $this->generatePointTransactionNo(),
                        'transaction_type' => 'order_credit',
                        'direction' => 'in',
                        'amount' => $pointAmount,
                        'balance_before' => $beforeBalance,
                        'balance_after' => $afterBalance,
                        'summary' => '배송완료 포인트 적립',
                        'description' => '주문번호 ' . $lockedOrder->order_no . ' 배송완료 처리',
                        'transacted_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        return redirect()
            ->route('suju-list.complete-popup', $order->order_no)
            ->with('success', '배송완료 처리되었습니다.');
    }

    public function uploadPhoto(Request $request, Order $order)
    {
        $user = $request->user();
        $shop = $user?->shop;

        abort_unless($shop, 403);
        abort_unless((int) $order->receiver_shop_id === (int) $shop->id, 403);

        $validated = $request->validate([
            'photo_field' => ['required', 'in:photo_shop,photo_site,photo_extra'],
            'photo_file' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/bmp,image/svg+xml'],
        ], [
            'photo_file.required' => '업로드할 사진을 선택해 주세요.',
            'photo_file.mimetypes' => '이미지 파일만 업로드할 수 있습니다.',
        ]);

        $field = $validated['photo_field'];

        $metaMap = [
            'photo_shop' => ['type' => 'other', 'sort' => 1],
            'photo_site' => ['type' => 'delivery_site', 'sort' => 2],
            'photo_extra' => ['type' => 'other', 'sort' => 3],
        ];

        $meta = $metaMap[$field];

        $uploaded = $this->uploadService->upload(
            $request->file('photo_file'),
            Cafe24FileUploadService::TYPE_PHOTO_SHARE
        );

        DB::transaction(function () use ($order, $user, $shop, $field, $meta, $uploaded) {
            DB::table('order_photos')
                ->where('order_id', $order->id)
                ->where('photo_type', $meta['type'])
                ->where('sort_order', $meta['sort'])
                ->delete();

            DB::table('order_photos')->insert([
                'order_id' => $order->id,
                'photo_type' => $meta['type'],
                'file_path' => $uploaded['url'],
                'sort_order' => $meta['sort'],
                'uploaded_by_user_id' => $user->id,
                'created_at' => now(),
            ]);

            $shopDisplayName = $this->makeShopDisplayName($shop);

            $photoLabelMap = [
                'photo_shop' => '매장사진',
                'photo_site' => '현장사진',
                'photo_extra' => '추가사진',
            ];

            $photoLabel = $photoLabelMap[$field] ?? '사진';

            DB::table('order_histories')->insert([
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'history_type' => 'updated',
                'message' => '수주사 <strong>' . $shopDisplayName . '</strong> ' . $photoLabel . ' 업로드 완료',
                'processed_at' => now(),
                'actor_user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => '사진이 업로드되었습니다.',
            'reload' => true,
            'reload_parent' => true,
        ]);
    }

    public function photoPopup(Request $request, Order $order)
    {
        $shop = $request->user()?->shop;

        abort_unless($shop, 403);
        abort_unless((int) $order->receiver_shop_id === (int) $shop->id, 403);

        $photos = DB::table('order_photos')
            ->where('order_id', $order->id)
            ->orderBy('sort_order')
            ->get();

        return view('pages.order-photo-popup', [
            'order' => $order,
            'photos' => $photos,
        ]);
    }
    protected function makeShopDisplayName(?Shop $shop): string
    {
        if (!$shop) {
            return '미지정 화원';
        }

        $regionLabel = DB::table('shop_delivery_areas')
            ->join('regions', 'shop_delivery_areas.region_id', '=', 'regions.id')
            ->where('shop_delivery_areas.shop_id', $shop->id)
            ->orderBy('shop_delivery_areas.id')
            ->value('regions.sido');

        return $shop->shop_name . ($regionLabel ? ' (' . $regionLabel . ')' : '');
    }

    protected function generatePointTransactionNo(): string
    {
        do {
            $transactionNo = 'PT' . now()->format('YmdHis') . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (DB::table('point_transactions')->where('transaction_no', $transactionNo)->exists());

        return $transactionNo;
    }
}
