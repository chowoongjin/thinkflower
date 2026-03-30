<?php

namespace App\Services;

use App\Models\Shop;
use Illuminate\Support\Facades\DB;

class ShopDisplayNameService
{
    public function format(?Shop $shop): string
    {
        if (!$shop) {
            return '미지정 화원';
        }

        if ((int) $shop->id === 1) {
            return '본부 수발주사업부';
        }

        $regionLabel = $this->regionLabel($shop);

        return $shop->shop_name . ($regionLabel ? ' (' . $regionLabel . ')' : '');
    }

    public function regionLabel(?Shop $shop): ?string
    {
        if (!$shop) {
            return null;
        }

        return DB::table('shop_delivery_areas')
            ->join('regions', 'shop_delivery_areas.region_id', '=', 'regions.id')
            ->where('shop_delivery_areas.shop_id', $shop->id)
            ->orderBy('shop_delivery_areas.id')
            ->value('regions.sido');
    }
}
