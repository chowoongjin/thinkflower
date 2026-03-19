<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Shop;
use App\Models\User;
use App\Services\Cafe24FileUploadService;
use Illuminate\Support\Facades\DB;
use Throwable;

class RegisterController extends Controller
{
    protected Cafe24FileUploadService $uploadService;

    public function __construct(Cafe24FileUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function create()
    {
        return view('pages.register');
    }

    public function store(RegisterRequest $request)
    {
        $validated = $request->validated();

        try {
            $uploaded = $this->uploadService->upload(
                $request->file('business_license'),
                Cafe24FileUploadService::TYPE_BUSINESS_LICENSE
            );
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'business_license' => '사업자등록증 업로드에 실패했습니다. 잠시 후 다시 시도해 주세요.',
                ]);
        }

        DB::transaction(function () use ($validated, $uploaded) {
            $shop = Shop::create([
                'shop_name' => $validated['shop_name'],
                'owner_name' => $validated['owner_name'],
                'business_no' => $validated['business_no'],
                'business_license_path' => $uploaded['url'],
                'business_addr1' => $validated['business_addr1'],
                'business_addr2' => $validated['business_addr2'],
                'tax_email' => $validated['tax_email'],
                'main_phone' => $validated['main_phone'],
                'fax' => $validated['fax'],
                'career_years_label' => $validated['career_years_label'],
                'bank_name' => $validated['bank_name'],
                'bank_account' => $validated['bank_account'],
                'bank_holder' => $validated['bank_holder'],
                'status' => 'pending',
                'is_active' => true,
            ]);

            User::create([
                'login_id' => $validated['login_id'],
                'name' => $validated['owner_name'],
                'password' => $validated['password'],
                'role' => 'member',
                'shop_id' => $shop->id,
            ]);

            $this->syncProducts($shop->id, $validated['products']);
            $this->syncDeliveryAreas($shop->id, $validated['delivery_areas']);
        });

        return redirect()
            ->route('login')
            ->with('success', '회원가입이 완료되었습니다. 로그인해 주세요.');
    }

    protected function syncProducts(int $shopId, string $products): void
    {
        $productNames = collect(explode(',', $products))
            ->map(fn ($v) => trim($v))
            ->filter()
            ->unique()
            ->values();

        if ($productNames->isEmpty()) {
            return;
        }

        $productIds = DB::table('product_categories')
            ->whereIn('name', $productNames)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($productIds)) {
            return;
        }

        $rows = collect($productIds)
            ->map(fn ($productId) => [
                'shop_id' => $shopId,
                'product_category_id' => $productId,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->values()
            ->all();

        DB::table('shop_products')->insert($rows);
    }

    protected function syncDeliveryAreas(int $shopId, string $deliveryAreas): void
    {
        $areaNames = collect(explode(',', $deliveryAreas))
            ->map(fn ($v) => trim($v))
            ->filter()
            ->unique()
            ->values();

        if ($areaNames->isEmpty()) {
            return;
        }

        $rows = [];

        foreach ($areaNames as $areaName) {
            $parts = preg_split('/\s+/', $areaName);

            if (count($parts) < 2) {
                continue;
            }

            $sido = $parts[0];
            $sigungu = $parts[1];

            if ($sido === '세종특별자치시' && isset($parts[2])) {
                $sigungu = $parts[1] . ' ' . $parts[2];
            }

            $regionId = DB::table('regions')
                ->where('sido', $sido)
                ->where('sigungu', $sigungu)
                ->value('id');

            if ($regionId) {
                $rows[] = [
                    'shop_id' => $shopId,
                    'region_id' => $regionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (empty($rows)) {
            return;
        }

        $rows = collect($rows)
            ->unique(fn ($row) => $row['shop_id'] . ':' . $row['region_id'])
            ->values()
            ->all();

        DB::table('shop_delivery_areas')->insert($rows);
    }
}
