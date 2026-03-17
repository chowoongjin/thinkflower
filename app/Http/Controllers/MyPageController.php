<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyPageController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $shop = $user?->shop;

        abort_unless($shop, 403);

        $shop->load([
            'handledProductCategories',
            'deliveryAreas.region',
        ]);

        $businessAddress = trim(collect([
            $shop->business_zipcode ?? null,
            $shop->business_addr1 ?? null,
            $shop->business_addr2 ?? null,
        ])->filter()->implode(' '));

        if ($businessAddress === '') {
            $businessAddress = '-';
        }

        $handledProductsText = $shop->handledProductCategories
            ->pluck('name')
            ->filter()
            ->implode(', ');

        if ($handledProductsText === '') {
            $handledProductsText = null;
        }

        $deliveryRegionsText = $shop->deliveryAreas
            ->map(function ($area) {
                $region = $area->region;

                if (!$region) {
                    return null;
                }

                return trim(collect([
                    $region->sido ?? null,
                    $region->sigungu ?? null,
                    $region->eup_myeon_dong ?? null,
                ])->filter()->implode(' '));
            })
            ->filter()
            ->unique()
            ->implode(', ');

        if ($deliveryRegionsText === '') {
            $deliveryRegionsText = null;
        }

        return view('pages.my-page', [
            'shop' => $shop,
            'businessAddress' => $businessAddress,
            'handledProductsText' => $handledProductsText,
            'deliveryRegionsText' => $deliveryRegionsText,
        ]);
    }

    public function businessInfoModal(Request $request)
    {
        $user = $request->user();
        $shop = $user?->shop;

        abort_unless($shop, 403);

        $businessAddress = trim(collect([
            $shop->business_addr1 ?? null,
            $shop->business_addr2 ?? null,
        ])->filter()->implode(' '));

        return view('modals.my-page-business-info-modal', [
            'shop' => $shop,
            'businessAddress' => $businessAddress,
        ]);
    }

    public function updateBusinessInfo(Request $request)
    {
        $user = $request->user();
        $shop = $user?->shop;

        abort_unless($shop, 403);

        $validated = $request->validate([
            'business_no' => ['required', 'string', 'max:30'],
            'owner_name' => ['required', 'string', 'max:100'],
            'business_addr1' => ['required', 'string', 'max:255'],
            'tax_email' => ['nullable', 'email', 'max:255'],
        ], [
            'business_no.required' => '사업자번호를 입력해 주세요.',
            'owner_name.required' => '대표자명을 입력해 주세요.',
            'business_addr1.required' => '사업자소재지를 입력해 주세요.',
            'tax_email.email' => '계산서수령 이메일 형식이 올바르지 않습니다.',
        ]);

        $shop->update([
            'business_no' => $validated['business_no'],
            'owner_name' => $validated['owner_name'],
            'business_addr1' => $validated['business_addr1'],
            'tax_email' => $validated['tax_email'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => '사업자 정보가 수정되었습니다.',
            'redirect' => route('my-page.show'),
        ]);
    }

    public function shopInfoModal(Request $request)
    {
        $user = $request->user();
        $shop = $user?->shop;

        abort_unless($shop, 403);

        return view('modals.my-page-shop-info-modal', [
            'shop' => $shop,
        ]);
    }

    public function updateShopInfo(Request $request)
    {
        $user = $request->user();
        $shop = $user?->shop;

        abort_unless($shop, 403);

        $validated = $request->validate([
            'main_phone' => ['required', 'string', 'max:30'],
            'sub_phone' => ['nullable', 'string', 'max:30'],
            'fax' => ['nullable', 'string', 'max:30'],
            'shop_name' => ['required', 'string', 'max:100'],
        ], [
            'main_phone.required' => '대표 연락망을 입력해 주세요.',
            'shop_name.required' => '화원사명을 입력해 주세요.',
        ]);

        $shop->update([
            'main_phone' => $validated['main_phone'],
            'sub_phone' => $validated['sub_phone'] ?? null,
            'fax' => $validated['fax'] ?? null,
            'shop_name' => $validated['shop_name'],
        ]);

        return response()->json([
            'success' => true,
            'message' => '매장운영 정보가 수정되었습니다.',
            'redirect' => route('my-page.show'),
        ]);
    }

    public function settlementInfoModal(Request $request)
    {
        $user = $request->user();
        $shop = $user?->shop;

        abort_unless($shop, 403);

        return view('modals.my-page-settlement-info-modal', [
            'shop' => $shop,
        ]);
    }

    public function updateSettlementInfo(Request $request)
    {
        $user = $request->user();
        $shop = $user?->shop;

        abort_unless($shop, 403);

        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_holder' => ['required', 'string', 'max:100'],
            'bank_account' => ['required', 'string', 'max:100'],
        ], [
            'bank_name.required' => '입금은행을 입력해 주세요.',
            'bank_holder.required' => '예금주명을 입력해 주세요.',
            'bank_account.required' => '계좌번호를 입력해 주세요.',
        ]);

        $shop->update([
            'bank_name' => $validated['bank_name'],
            'bank_holder' => $validated['bank_holder'],
            'bank_account' => $validated['bank_account'],
        ]);

        return response()->json([
            'success' => true,
            'message' => '정산 정보가 수정되었습니다.',
            'redirect' => route('my-page.show'),
        ]);
    }
}
