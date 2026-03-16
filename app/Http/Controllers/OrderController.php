<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\PointTransaction;
use App\Models\Shop;
use App\Services\Cafe24FileUploadService;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected Cafe24FileUploadService $uploadService;
    protected PointService $pointService;

    public function __construct(
        Cafe24FileUploadService $uploadService,
        PointService $pointService
    ) {
        $this->uploadService = $uploadService;
        $this->pointService = $pointService;
    }

    public function store(Request $request)
    {
        $request->merge([
            'original_amount' => (int) preg_replace('/[^0-9]/', '', (string) $request->input('original_amount')),
            'order_amount' => (int) preg_replace('/[^0-9]/', '', (string) $request->input('order_amount')),
            'delivery_hour' => $request->filled('delivery_hour') ? (int) $request->input('delivery_hour') : null,
            'delivery_minute' => $request->filled('delivery_minute') ? (int) $request->input('delivery_minute') : null,
        ]);

        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:100'],
            'product_detail' => ['required', 'string', 'max:255'],
            'product_image_file' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/bmp,image/svg+xml'],
            'product_image_input_url' => ['nullable', 'url', 'max:255'],
            'original_amount' => ['required', 'integer', 'min:0'],
            'order_amount' => ['required', 'integer', 'min:0'],
            'delivery_addr1' => ['required', 'string', 'max:255'],
            'delivery_addr2' => ['nullable', 'string', 'max:255'],
            'delivery_date' => ['required', 'date'],
            'delivery_hour' => ['nullable', 'integer', 'between:0,23'],
            'delivery_minute' => ['nullable', 'integer', 'in:0,10,20,30,40,50'],
            'delivery_time_type' => ['required', 'in:도착,예식,행사'],
            'is_urgent' => ['nullable', 'boolean'],
            'recipient_name' => ['required', 'string', 'max:50'],
            'recipient_phone' => ['required', 'string', 'max:30'],
            'ribbon_phrase' => ['required', 'string', 'max:100'],
            'sender_name' => ['required', 'string', 'max:100'],
            'card_message' => ['nullable', 'string', 'max:255'],
            'request_note' => ['required', 'string', 'max:255'],
            'request_photo' => ['nullable', 'boolean'],
        ], [
            'product_name.required' => '상품을 선택해 주세요.',
            'product_detail.required' => '상품상세를 입력해 주세요.',
            'product_image_file.mimetypes' => '상품이미지는 이미지 파일만 업로드할 수 있습니다.',
            'product_image_input_url.url' => '상품이미지 URL 형식이 올바르지 않습니다.',
            'original_amount.required' => '원청금액을 입력해 주세요.',
            'original_amount.integer' => '원청금액은 숫자만 입력해 주세요.',
            'order_amount.required' => '발주금액을 입력해 주세요.',
            'order_amount.integer' => '발주금액은 숫자만 입력해 주세요.',
            'delivery_addr1.required' => '배달요청장소를 입력해 주세요.',
            'delivery_date.required' => '배달요청일시를 입력해 주세요.',
            'delivery_hour.integer' => '배달 시간은 숫자 형식이어야 합니다.',
            'delivery_minute.integer' => '배달 분은 숫자 형식이어야 합니다.',
            'delivery_time_type.required' => '도착 / 예식 / 행사 중 하나를 선택해 주세요.',
            'delivery_time_type.in' => '도착 / 예식 / 행사 중 하나를 선택해 주세요.',
            'recipient_name.required' => '받는 분 성함을 입력해 주세요.',
            'recipient_phone.required' => '받는 분 연락처를 입력해 주세요.',
            'ribbon_phrase.required' => '경조사어를 입력해 주세요.',
            'sender_name.required' => '보내는분을 입력해 주세요.',
            'request_note.required' => '고객요청사항을 입력해 주세요.',
        ]);

        $user = $request->user();
        $shop = $user?->shop;

        abort_unless($user && $shop, 403);

        $orderAmount = (int) $validated['order_amount'];
        $productImagePath = null;
        $productImageUrl = null;

        if ($request->hasFile('product_image_file')) {
            $uploaded = $this->uploadService->upload(
                $request->file('product_image_file'),
                Cafe24FileUploadService::TYPE_PRODUCT_IMAGE
            );

            $productImagePath = $uploaded['url'];
            $productImageUrl = $uploaded['url'];
        } elseif (!empty($validated['product_image_input_url'])) {
            $productImagePath = $validated['product_image_input_url'];
            $productImageUrl = $validated['product_image_input_url'];
        }

        DB::transaction(function () use (
            $validated,
            $user,
            $shop,
            $orderAmount,
            $productImagePath,
            $productImageUrl
        ) {
            $ordererShop = Shop::where('id', $shop->id)
                ->lockForUpdate()
                ->firstOrFail();

            $order = Order::create([
                'order_no' => $this->generateOrderNo(),
                'order_type' => 'direct',
                'brokerage_type' => 'waiting',
                'orderer_shop_id' => $ordererShop->id,
                'receiver_shop_id' => null,
                'created_user_id' => $user->id,
                'created_channel' => 'member',
                'created_by_admin_user_id' => null,

                'product_category_id' => null,
                'product_name' => $validated['product_name'],
                'product_detail' => $validated['product_detail'],
                'product_image_path' => $productImagePath,
                'product_image_url' => $productImageUrl,

                'original_amount' => (int) $validated['original_amount'],
                'order_amount' => $orderAmount,
                'supply_amount' => $orderAmount,
                'brokerage_fee' => 0,
                'payment_amount' => $orderAmount,
                'point_used_amount' => $orderAmount,
                'point_earned_amount' => $orderAmount,

                'delivery_zipcode' => null,
                'delivery_addr1' => $validated['delivery_addr1'],
                'delivery_addr2' => $validated['delivery_addr2'] ?? null,
                'delivery_place' => null,
                'delivery_date' => $validated['delivery_date'],
                'delivery_hour' => $validated['delivery_hour'],
                'delivery_minute' => $validated['delivery_minute'],
                'delivery_time_type' => $validated['delivery_time_type'],
                'is_urgent' => (bool) ($validated['is_urgent'] ?? false),

                'recipient_name' => $validated['recipient_name'],
                'recipient_phone' => $validated['recipient_phone'],
                'ribbon_phrase' => $validated['ribbon_phrase'],
                'sender_name' => $validated['sender_name'],
                'card_message' => $validated['card_message'] ?? null,
                'request_note' => $validated['request_note'] ?? null,
                'request_photo' => (bool) ($validated['request_photo'] ?? false),

                'current_status' => 'submitted',
            ]);

            $ordererRegion = DB::table('shop_delivery_areas')
                ->join('regions', 'shop_delivery_areas.region_id', '=', 'regions.id')
                ->where('shop_delivery_areas.shop_id', $ordererShop->id)
                ->orderBy('shop_delivery_areas.id')
                ->value('regions.sido');

            $ordererDisplayName = ($ordererShop->shop_name ?? '미지정 화원')
                . ($ordererRegion ? '(' . $ordererRegion . ')' : '');

            OrderHistory::create([
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'history_type' => 'created',
                'message' => '발주사 <strong>' . $ordererDisplayName . '</strong> 로 부터 발주 등록',
                'processed_at' => now(),
                'actor_user_id' => $user->id,
            ]);

            $this->pointService->debitForOrder(
                $ordererShop,
                $user,
                $order,
                $orderAmount,
                '발주 포인트 차감'
            );
        });

        return redirect()
            ->route('bonbu-balju')
            ->with('success_redirect_order_list', '발주서가 정상적으로 등록되었습니다.');
    }

    public function storeHq(Request $request)
    {
        $request->merge([
            'original_amount' => (int) preg_replace('/[^0-9]/', '', (string) $request->input('original_amount')),
            'order_amount' => (int) preg_replace('/[^0-9]/', '', (string) $request->input('order_amount')),
            'delivery_hour' => $request->filled('delivery_hour') ? (int) $request->input('delivery_hour') : null,
            'delivery_minute' => $request->filled('delivery_minute') ? (int) $request->input('delivery_minute') : null,
        ]);

        $validated = $request->validate([
            'orderer_shop_id' => ['required', 'integer', 'exists:shops,id'],
            'receiver_shop_id' => ['nullable', 'integer', 'exists:shops,id'],

            'product_name' => ['required', 'string', 'max:100'],
            'product_detail' => ['required', 'string', 'max:255'],
            'product_image_file' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/bmp,image/svg+xml'],
            'product_image_input_url' => ['nullable', 'url', 'max:255'],
            'original_amount' => ['required', 'integer', 'min:0'],
            'order_amount' => ['required', 'integer', 'min:0'],
            'delivery_addr1' => ['required', 'string', 'max:255'],
            'delivery_addr2' => ['nullable', 'string', 'max:255'],
            'delivery_date' => ['required', 'date'],
            'delivery_hour' => ['nullable', 'integer', 'between:0,23'],
            'delivery_minute' => ['nullable', 'integer', 'in:0,10,20,30,40,50'],
            'delivery_time_type' => ['required', 'in:도착,예식,행사'],
            'is_urgent' => ['nullable', 'boolean'],
            'recipient_name' => ['required', 'string', 'max:50'],
            'recipient_phone' => ['required', 'string', 'max:30'],
            'ribbon_phrase' => ['required', 'string', 'max:100'],
            'sender_name' => ['required', 'string', 'max:100'],
            'card_message' => ['nullable', 'string', 'max:255'],
            'request_note' => ['required', 'string', 'max:255'],
            'request_photo' => ['nullable', 'boolean'],
        ], [
            'orderer_shop_id.required' => '발주화원사를 선택해 주세요.',
            'orderer_shop_id.exists' => '유효한 발주화원사가 아닙니다.',
            'receiver_shop_id.exists' => '유효한 수주화원사가 아닙니다.',
            'product_name.required' => '상품을 선택해 주세요.',
            'product_detail.required' => '상품상세를 입력해 주세요.',
            'product_image_file.mimetypes' => '상품이미지는 이미지 파일만 업로드할 수 있습니다.',
            'product_image_input_url.url' => '상품이미지 URL 형식이 올바르지 않습니다.',
            'original_amount.required' => '원청금액을 입력해 주세요.',
            'original_amount.integer' => '원청금액은 숫자만 입력해 주세요.',
            'order_amount.required' => '발주금액을 입력해 주세요.',
            'order_amount.integer' => '발주금액은 숫자만 입력해 주세요.',
            'delivery_addr1.required' => '배달요청장소를 입력해 주세요.',
            'delivery_date.required' => '배달요청일시를 입력해 주세요.',
            'delivery_hour.integer' => '배달 시간은 숫자 형식이어야 합니다.',
            'delivery_minute.integer' => '배달 분은 숫자 형식이어야 합니다.',
            'delivery_time_type.required' => '도착 / 예식 / 행사 중 하나를 선택해 주세요.',
            'delivery_time_type.in' => '도착 / 예식 / 행사 중 하나를 선택해 주세요.',
            'recipient_name.required' => '받는 분 성함을 입력해 주세요.',
            'recipient_phone.required' => '받는 분 연락처를 입력해 주세요.',
            'ribbon_phrase.required' => '경조사어를 입력해 주세요.',
            'sender_name.required' => '보내는분을 입력해 주세요.',
            'request_note.required' => '고객요청사항을 입력해 주세요.',
        ]);

        $user = $request->user();

        if (!$user || !in_array($user->role, ['admin', 'hq'], true)) {
            abort(403);
        }

        $orderAmount = (int) $validated['order_amount'];
        $productImagePath = null;
        $productImageUrl = null;

        if ($request->hasFile('product_image_file')) {
            $uploaded = $this->uploadService->upload(
                $request->file('product_image_file'),
                Cafe24FileUploadService::TYPE_PRODUCT_IMAGE
            );

            $productImagePath = $uploaded['url'];
            $productImageUrl = $uploaded['url'];
        } elseif (!empty($validated['product_image_input_url'])) {
            $productImagePath = $validated['product_image_input_url'];
            $productImageUrl = $validated['product_image_input_url'];
        }

        DB::transaction(function () use (
            $validated,
            $user,
            $orderAmount,
            $productImagePath,
            $productImageUrl
        ) {
            $ordererShop = Shop::where('id', $validated['orderer_shop_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $receiverShop = !empty($validated['receiver_shop_id'])
                ? Shop::findOrFail($validated['receiver_shop_id'])
                : null;

            $order = Order::create([
                'order_no' => $this->generateOrderNo(),
                'order_type' => 'hq',
                'brokerage_type' => $receiverShop ? 'assigned' : 'waiting',
                'orderer_shop_id' => $ordererShop->id,
                'receiver_shop_id' => $receiverShop?->id,
                'created_user_id' => $user->id,
                'created_channel' => 'admin_manual',
                'created_by_admin_user_id' => $user->id,

                'product_category_id' => null,
                'product_name' => $validated['product_name'],
                'product_detail' => $validated['product_detail'],
                'product_image_path' => $productImagePath,
                'product_image_url' => $productImageUrl,

                'original_amount' => (int) $validated['original_amount'],
                'order_amount' => $orderAmount,
                'supply_amount' => $orderAmount,
                'brokerage_fee' => 0,
                'payment_amount' => $orderAmount,
                'point_used_amount' => $orderAmount,
                'point_earned_amount' => $orderAmount,

                'delivery_zipcode' => null,
                'delivery_addr1' => $validated['delivery_addr1'],
                'delivery_addr2' => $validated['delivery_addr2'] ?? null,
                'delivery_place' => null,
                'delivery_date' => $validated['delivery_date'],
                'delivery_hour' => $validated['delivery_hour'],
                'delivery_minute' => $validated['delivery_minute'],
                'delivery_time_type' => $validated['delivery_time_type'],
                'is_urgent' => (bool) ($validated['is_urgent'] ?? false),

                'recipient_name' => $validated['recipient_name'],
                'recipient_phone' => $validated['recipient_phone'],
                'ribbon_phrase' => $validated['ribbon_phrase'],
                'sender_name' => $validated['sender_name'],
                'card_message' => $validated['card_message'] ?? null,
                'request_note' => $validated['request_note'] ?? null,
                'request_photo' => (bool) ($validated['request_photo'] ?? false),

                'current_status' => 'submitted',
            ]);

            $ordererRegion = DB::table('shop_delivery_areas')
                ->join('regions', 'shop_delivery_areas.region_id', '=', 'regions.id')
                ->where('shop_delivery_areas.shop_id', $ordererShop->id)
                ->orderBy('shop_delivery_areas.id')
                ->value('regions.sido');

            $ordererDisplayName = ($ordererShop->shop_name ?? '미지정 화원')
                . ($ordererRegion ? '(' . $ordererRegion . ')' : '');

            OrderHistory::create([
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'history_type' => 'created',
                'message' => '발주사 <strong>' . $ordererDisplayName . '</strong> 로 부터 발주 등록',
                'processed_at' => now(),
                'actor_user_id' => $user->id,
            ]);

            $this->pointService->debitForOrder(
                $ordererShop,
                $user,
                $order,
                $orderAmount,
                '본부발주 포인트 차감'
            );
        });

        return redirect()
            ->route('bonbu-balju')
            ->with('success_redirect_order_list', '발주서가 정상적으로 등록되었습니다.');
    }

    protected function generateOrderNo(): string
    {
        do {
            $orderNo = str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (Order::where('order_no', $orderNo)->exists());

        return $orderNo;
    }

    protected function generatePointTransactionNo(): string
    {
        do {
            $transactionNo = 'PT' . now()->format('YmdHis') . strtoupper(Str::random(6));
        } while (PointTransaction::where('transaction_no', $transactionNo)->exists());

        return $transactionNo;
    }
}
