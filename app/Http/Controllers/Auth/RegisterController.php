<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use App\Services\Cafe24FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function store(Request $request)
    {
        $request->merge([
            'tax_email' => trim(($request->tax_email_id ?? '') . '@' . ($request->tax_email_domain ?? '')),
        ]);

        $validated = $request->validate([
            'login_id' => ['required', 'string', 'min:4', 'max:50', 'unique:users,login_id'],
            'password' => ['required', 'string', 'min:5', 'confirmed'],
            'business_no' => ['required', 'string', 'max:20', 'unique:shops,business_no'],
            'owner_name' => ['required', 'string', 'max:50'],
            'business_addr1' => ['required', 'string', 'max:255'],
            'business_addr2' => ['required', 'string', 'max:255'],
            'business_license' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/gif,image/webp,application/pdf'],
            'tax_email' => ['required', 'email', 'max:100'],
            'shop_name' => ['required', 'string', 'max:100'],
            'career_years_label' => ['required', 'string', 'max:30'],
            'main_phone' => ['required', 'string', 'max:30'],
            'fax' => ['required', 'string', 'max:30'],
            'products' => ['required', 'string', 'max:255'],
            'delivery_areas' => ['required', 'string'],
            'bank_name' => ['required', 'string', 'max:50'],
            'bank_holder' => ['required', 'string', 'max:100'],
            'bank_account' => ['required', 'string', 'max:50'],
            'agree_service' => ['accepted'],
            'agree_privacy' => ['accepted'],
            'agree_third_party' => ['accepted'],
        ], [
            'login_id.required' => '아이디를 입력해 주세요.',
            'password.required' => '비밀번호를 입력해 주세요.',
            'password.confirmed' => '비밀번호 확인이 일치하지 않습니다.',
            'business_no.required' => '사업자등록번호를 입력해 주세요.',
            'owner_name.required' => '대표자명을 입력해 주세요.',
            'business_addr1.required' => '주소를 입력해 주세요.',
            'business_addr2.required' => '상세주소를 입력해 주세요.',
            'business_license.required' => '사업자등록증을 업로드해 주세요.',
            'business_license.mimetypes' => '사업자등록증은 이미지 또는 PDF 파일만 업로드할 수 있습니다.',
            'tax_email.required' => '계산서 수령 이메일을 입력해 주세요.',
            'tax_email.email' => '계산서 수령 이메일 형식이 올바르지 않습니다.',
            'shop_name.required' => '화원사명을 입력해 주세요.',
            'career_years_label.required' => '화원 운영경력을 선택해 주세요.',
            'main_phone.required' => '대표 연락망을 입력해 주세요.',
            'fax.required' => '팩스 수신번호를 입력해 주세요.',
            'products.required' => '수주 취급상품을 선택해 주세요.',
            'delivery_areas.required' => '배송 가능지역을 선택해 주세요.',
            'bank_name.required' => '입금은행을 선택해 주세요.',
            'bank_holder.required' => '예금주명을 입력해 주세요.',
            'bank_account.required' => '입금 계좌번호를 입력해 주세요.',
            'agree_service.accepted' => '서비스 이용약관에 동의해 주세요.',
            'agree_privacy.accepted' => '개인정보 수집 및 이용에 동의해 주세요.',
            'agree_third_party.accepted' => '개인정보 제3자 제공에 동의해 주세요.',
        ]);

        $uploaded = $this->uploadService->upload(
            $request->file('business_license'),
            Cafe24FileUploadService::TYPE_BUSINESS_LICENSE
        );

        DB::transaction(function () use ($validated, $uploaded) {
            $shop = Shop::create([
                'shop_name' => $validated['shop_name'],
                'owner_name' => $validated['owner_name'],
                'business_no' => preg_replace('/[^0-9]/', '', $validated['business_no']),
                'business_license_path' => $uploaded['url'],
                'business_license_url' => $uploaded['url'],
                'business_addr1' => $validated['business_addr1'],
                'business_addr2' => $validated['business_addr2'] ?? null,
                'tax_email' => $validated['tax_email'],
                'main_phone' => preg_replace('/[^0-9]/', '', $validated['main_phone']),
                'fax' => isset($validated['fax']) ? preg_replace('/[^0-9]/', '', $validated['fax']) : null,
                'career_years_label' => $validated['career_years_label'],
                'product_summary' => $validated['products'],
                'delivery_area_summary' => $validated['delivery_areas'],
                'bank_name' => $validated['bank_name'],
                'bank_account' => preg_replace('/[^0-9]/', '', $validated['bank_account']),
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

            // 상품명 -> product_categories.id 매핑
            $productNames = collect(explode(',', (string) $validated['products']))
                ->map(fn ($v) => trim($v))
                ->filter()
                ->unique()
                ->values();

            $productIds = DB::table('product_categories')
                ->whereIn('name', $productNames)
                ->pluck('id', 'name');

            foreach ($productNames as $productName) {
                $productId = $productIds[$productName] ?? null;

                if ($productId) {
                    DB::table('shop_products')->insert([
                        'shop_id' => $shop->id,
                        'product_category_id' => $productId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 지역명 -> regions.id 매핑
            $areaNames = collect(explode(',', (string) $validated['delivery_areas']))
                ->map(fn ($v) => trim($v))
                ->filter()
                ->unique()
                ->values();

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
                    DB::table('shop_delivery_areas')->insert([
                        'shop_id' => $shop->id,
                        'region_id' => $regionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        return redirect()->route('login')->with('success', '회원가입이 완료되었습니다. 로그인해 주세요.');
    }
}
