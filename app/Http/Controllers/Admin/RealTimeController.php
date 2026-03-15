<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\ProductCategory;
use App\Models\Shop;
use App\Services\Cafe24FileUploadService;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RealTimeController extends Controller
{
    protected int $hqShopId = 1;
    protected Cafe24FileUploadService $uploadService;
    protected PointService $pointService;

    public function __construct(
        Cafe24FileUploadService $uploadService,
        PointService $pointService
    ) {
        $this->uploadService = $uploadService;
        $this->pointService = $pointService;
    }

    protected function makeShopDisplayName(?Shop $shop): string
    {
        if (!$shop) {
            return '미지정 화원';
        }

        if ((int) $shop->id === 1) {
            return '본부 수발주사업부';
        }

        $regionLabel = DB::table('shop_delivery_areas')
            ->join('regions', 'shop_delivery_areas.region_id', '=', 'regions.id')
            ->where('shop_delivery_areas.shop_id', $shop->id)
            ->orderBy('shop_delivery_areas.id')
            ->value('regions.sido');

        return $shop->shop_name . ($regionLabel ? ' (' . $regionLabel . ')' : '');
    }

    protected function createRealtimeOrderHistories(Order $order, Shop $ordererShop, Shop $receiverShop, int $actorUserId): void
    {
        $ordererDisplayName = $this->makeShopDisplayName($ordererShop);
        $receiverDisplayName = $this->makeShopDisplayName($receiverShop);

        OrderHistory::create([
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'history_type' => 'created',
            'message' => '발주사 <strong>' . $ordererDisplayName . '</strong> 에서 본부발주 등록',
            'processed_at' => now(),
            'actor_user_id' => $actorUserId,
        ]);

        OrderHistory::create([
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'history_type' => 'updated',
            'message' => '<strong>본부 수발주사업부</strong> 에서 수주사 중개 시작',
            'processed_at' => now(),
            'actor_user_id' => $actorUserId,
        ]);

        OrderHistory::create([
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'history_type' => 'accepted',
            'message' => '수주사 <strong>' . $receiverDisplayName . '</strong> 에서 배송가능 여부 확인',
            'processed_at' => now(),
            'actor_user_id' => $actorUserId,
        ]);
    }
    public function index()
    {
        $now = now();

        $generalNotices = Notice::query()
            ->where('category', 'general')
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $specialNotices = Notice::query()
            ->where('category', 'special')
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            })
            ->orderByDesc('is_pinned')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.real-time-balju', compact('generalNotices', 'specialNotices'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'original_amount' => (int) preg_replace('/[^0-9]/', '', (string) $request->input('original_amount')),
            'order_amount' => (int) preg_replace('/[^0-9]/', '', (string) $request->input('order_amount')),
            'delivery_hour' => $request->filled('delivery_hour') ? (int) $request->input('delivery_hour') : null,
            'delivery_minute' => $request->filled('delivery_minute') ? (int) $request->input('delivery_minute') : null,
            'request_photo' => $request->boolean('request_photo'),
            'is_urgent' => $request->boolean('is_urgent'),
            'orderer_is_hq' => $request->boolean('orderer_is_hq'),
            'receiver_is_hq' => $request->boolean('receiver_is_hq'),
        ]);

        $validated = $request->validate([
            'orderer_shop_id' => ['nullable', 'integer', 'exists:shops,id'],
            'receiver_shop_id' => ['nullable', 'integer', 'exists:shops,id'],

            'orderer_is_hq' => ['nullable', 'boolean'],
            'receiver_is_hq' => ['nullable', 'boolean'],

            'product_name' => ['required', 'string', 'max:100'],
            'product_detail' => ['required', 'string', 'max:255'],
            'product_image_file' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/bmp,image/svg+xml'],
            'product_image_url' => ['nullable', 'url', 'max:255'],

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
            'orderer_shop_id.exists' => '유효한 발주화원이 아닙니다.',
            'receiver_shop_id.exists' => '유효한 수주화원이 아닙니다.',
            'product_name.required' => '상품을 선택해 주세요.',
            'product_detail.required' => '상품상세를 입력해 주세요.',
            'original_amount.required' => '원청금액을 입력해 주세요.',
            'order_amount.required' => '발주금액을 입력해 주세요.',
            'delivery_addr1.required' => '배달요청장소를 입력해 주세요.',
            'delivery_date.required' => '배달요청일시를 입력해 주세요.',
            'delivery_time_type.required' => '도착 / 예식 / 행사 중 하나를 선택해 주세요.',
            'recipient_name.required' => '받는 분 성함을 입력해 주세요.',
            'recipient_phone.required' => '받는 분 연락처를 입력해 주세요.',
            'ribbon_phrase.required' => '경조사어를 입력해 주세요.',
            'sender_name.required' => '보내는분을 입력해 주세요.',
            'request_note.required' => '고객요청사항을 입력해 주세요.',
        ]);

        $user = $request->user();
        abort_unless($user && in_array($user->role, ['admin', 'hq'], true), 403);

        $ordererShopId = $validated['orderer_is_hq']
            ? $this->hqShopId
            : ($request->filled('orderer_shop_id') ? (int) $validated['orderer_shop_id'] : null);

        $receiverShopId = $validated['receiver_is_hq']
            ? $this->hqShopId
            : ($request->filled('receiver_shop_id') ? (int) $validated['receiver_shop_id'] : null);

        if ($ordererShopId === null) {
            throw ValidationException::withMessages([
                'orderer_shop_id' => '발주화원을 선택해 주세요.',
            ]);
        }

        if ($receiverShopId === null) {
            throw ValidationException::withMessages([
                'receiver_shop_id' => '수주화원을 선택해 주세요.',
            ]);
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
        } elseif (!empty($validated['product_image_url'])) {
            $productImagePath = $validated['product_image_url'];
            $productImageUrl = $validated['product_image_url'];
        }

        DB::transaction(function () use (
            $validated,
            $user,
            $ordererShopId,
            $receiverShopId,
            $orderAmount,
            $productImagePath,
            $productImageUrl
        ) {
            $ordererShop = Shop::where('id', $ordererShopId)->lockForUpdate()->firstOrFail();
            $receiverShop = Shop::findOrFail($receiverShopId);

            $beforePoint = (int) $ordererShop->current_point_balance;
            $pointPolicyType = $ordererShop->point_policy_type ?? 'prepaid';
            $creditLimit = (int) ($ordererShop->credit_limit ?? 0);
            $afterPoint = $beforePoint - $orderAmount;

            if ($pointPolicyType === 'prepaid') {
                if ($beforePoint < $orderAmount) {
                    throw ValidationException::withMessages([
                        'order_amount' => '보유 포인트가 부족하여 발주할 수 없습니다.',
                    ]);
                }
            } else {
                if ($creditLimit > 0 && $afterPoint < (-1 * $creditLimit)) {
                    throw ValidationException::withMessages([
                        'order_amount' => '후불 한도를 초과하여 발주할 수 없습니다.',
                    ]);
                }
            }

            $order = Order::create([
                'order_no' => $this->generateOrderNo(),
                'order_type' => 'hq',
                'brokerage_type' => 'assigned',
                'orderer_shop_id' => $ordererShop->id,
                'receiver_shop_id' => $receiverShop->id,
                'created_user_id' => $user->id,
                'created_channel' => 'admin_realtime',
                'created_by_admin_user_id' => $user->id,

                'product_category_id' => $this->resolveProductCategoryId($validated['product_name']),
                'product_name' => $validated['product_name'],
                'product_detail' => $validated['product_detail'],
                'product_image_path' => $productImagePath,
                'product_image_url' => $productImageUrl,

                'original_amount' => (int) $validated['original_amount'],
                'order_amount' => $orderAmount,
                'supply_amount' => 0,
                'brokerage_fee' => 0,
                'payment_amount' => $orderAmount,
                'point_used_amount' => $orderAmount,
                'point_earned_amount' => 0,

                'delivery_zipcode' => null,
                'delivery_addr1' => $validated['delivery_addr1'],
                'delivery_addr2' => null,
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
                'request_note' => $validated['request_note'],
                'request_photo' => (bool) ($validated['request_photo'] ?? false),

                'current_status' => 'submitted',
            ]);

            $this->createRealtimeOrderHistories(
                $order,
                $ordererShop,
                $receiverShop,
                $user->id
            );

            $this->pointService->debitForOrder(
                $ordererShop,
                $user,
                $order,
                $orderAmount,
                '관리자 실시간발주 포인트 차감'
            );
        });

        return redirect()
            ->route('admin.real-time.index')
            ->with('success', '실시간발주가 정상적으로 등록되었습니다.');
    }

    public function memberListPopup(Request $request)
    {
        $target = $request->input('target', 'receiver');
        $popupTitle = $target === 'orderer' ? '발주사선택' : '수주사선택';
        $keyword = trim((string) $request->input('keyword', ''));
        $productFilter = trim((string) $request->input('product_filter', ''));
        $sido = trim((string) $request->input('sido', ''));
        $sigungu = trim((string) $request->input('sigungu', ''));

        $regions = DB::table('regions')
            ->select('sido', 'sigungu')
            ->orderBy('sido')
            ->orderBy('sigungu')
            ->get();

        $sidoOptions = $regions
            ->pluck('sido')
            ->filter()
            ->unique()
            ->values();

        $sigunguOptions = $regions
            ->when($sido !== '', function ($collection) use ($sido) {
                return $collection->where('sido', $sido);
            })
            ->pluck('sigungu')
            ->filter()
            ->unique()
            ->sort(function ($a, $b) {
                $aIsAll = preg_match('/전체$/u', $a);
                $bIsAll = preg_match('/전체$/u', $b);

                if ($aIsAll && !$bIsAll) return -1;
                if (!$aIsAll && $bIsAll) return 1;

                return strcmp($a, $b);
            })
            ->values();

        $productMap = [
            '근조' => ['근조화환', '근조바구니', '근조쌀화환', '근조오브제'],
            '축하' => ['축하화환', '축하쌀화환'],
            '오브제' => ['근조오브제'],
            '쌀화환' => ['근조쌀화환', '축하쌀화환'],
            '관엽' => ['관엽식물'],
            '동양란' => ['동양란'],
            '서양란' => ['서양란'],
        ];

        $shops = Shop::query()
            ->leftJoinSub(
                DB::table('shop_delivery_areas as sda')
                    ->join('regions as r', 'sda.region_id', '=', 'r.id')
                    ->selectRaw('sda.shop_id, MIN(r.sido) as delivery_region')
                    ->groupBy('sda.shop_id'),
                'area_summary',
                'area_summary.shop_id',
                '=',
                'shops.id'
            )
            ->select('shops.*', 'area_summary.delivery_region')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('shops.shop_name', 'like', '%' . $keyword . '%')
                        ->orWhere('shops.owner_name', 'like', '%' . $keyword . '%')
                        ->orWhere('shops.business_no', 'like', '%' . $keyword . '%')
                        ->orWhere('shops.main_phone', 'like', '%' . $keyword . '%')
                        ->orWhere('area_summary.delivery_region', 'like', '%' . $keyword . '%');
                });
            })
            ->when($productFilter !== '' && $productFilter !== '전체', function ($query) use ($productFilter, $productMap) {
                $categories = $productMap[$productFilter] ?? [];

                if (!empty($categories)) {
                    $query->whereExists(function ($sub) use ($categories) {
                        $sub->select(DB::raw(1))
                            ->from('shop_products')
                            ->join('product_categories', 'shop_products.product_category_id', '=', 'product_categories.id')
                            ->whereColumn('shop_products.shop_id', 'shops.id')
                            ->whereIn('product_categories.name', $categories);
                    });
                }
            })
            ->when($sido !== '' || $sigungu !== '', function ($query) use ($sido, $sigungu) {
                $query->whereExists(function ($sub) use ($sido, $sigungu) {
                    $sub->select(DB::raw(1))
                        ->from('shop_delivery_areas as sda2')
                        ->join('regions as r2', 'sda2.region_id', '=', 'r2.id')
                        ->whereColumn('sda2.shop_id', 'shops.id');

                    if ($sido !== '') {
                        $sub->where('r2.sido', $sido);
                    }

                    if ($sigungu !== '') {
                        $sub->where('r2.sigungu', $sigungu);
                    }
                });
            })
            ->orderByDesc('shops.id')
            ->paginate(10)
            ->withQueryString();

        $data = compact(
            'shops',
            'keyword',
            'target',
            'popupTitle',
            'productFilter',
            'sido',
            'sigungu',
            'sidoOptions',
            'sigunguOptions'
        );

        if ($request->ajax()) {
            return view('admin.partials.member-list-popup-content', $data);
        }

        return view('admin.member-list-popup', $data);
    }

    protected function resolveProductCategoryId(string $productName): ?int
    {
        $map = [
            '근조3단(기본)' => '근조화환',
            '근조3단(고급)' => '근조화환',
            '근조3단(특대)' => '근조화환',
            '축하3단(기본)' => '축하화환',
            '축하3단(고급)' => '축하화환',
            '축하3단(특대)' => '축하화환',
            '꽃바구니' => '꽃바구니',
            '관엽식물' => '관엽식물',
            '동양란' => '동양란',
            '서양란' => '서양란',
            '근조바구니' => '근조바구니',
            '근조오브제' => '근조오브제',
            '근조쌀화환' => '근조쌀화환',
            '축하쌀화환' => '축하쌀화환',
        ];

        $categoryName = $map[$productName] ?? null;

        if (!$categoryName) {
            return null;
        }

        return ProductCategory::where('name', $categoryName)->value('id');
    }

    protected function generateOrderNo(): string
    {
        do {
            $orderNo = str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (Order::where('order_no', $orderNo)->exists());

        return $orderNo;
    }
}
