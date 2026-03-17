<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopDeliveryArea extends Model
{
    protected $table = 'shop_delivery_areas';

    protected $fillable = [
        'shop_id',
        'region_id',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
