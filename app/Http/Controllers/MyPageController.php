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

        return view('pages.modals.my-page-business-info-modal', [
            'shop' => $shop,
            'businessAddress' => $businessAddress,
        ]);
    }
}
