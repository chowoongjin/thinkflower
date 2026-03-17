<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'regions';

    public $timestamps = false;

    protected $fillable = [
        'sido',
        'sigungu',
        'eup_myeon_dong',
    ];

    public function shopDeliveryAreas()
    {
        return $this->hasMany(ShopDeliveryArea::class, 'region_id');
    }
}
