<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shop_name',
        'owner_name',
        'business_no',
        'business_license_path',
        'business_zipcode',
        'business_addr1',
        'business_addr2',
        'tax_email',
        'main_phone',
        'sub_phone',
        'fax',
        'career_years_label',
        'bank_name',
        'bank_account',
        'bank_holder',
        'current_point_balance',
        'status',
        'is_active',
        'approved_at',
        'rejected_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function handledProductCategories()
    {
        return $this->belongsToMany(
            ProductCategory::class,
            'shop_products',
            'shop_id',
            'product_category_id'
        );
    }

    public function deliveryAreas()
    {
        return $this->hasMany(ShopDeliveryArea::class, 'shop_id');
    }
}
