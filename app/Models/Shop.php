<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\PointTransaction;
use App\Models\ProductCategory;
use App\Models\ShopDeliveryArea;

class Shop extends Model
{
    use HasFactory, SoftDeletes;

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
        'admin_memo',
        'point_policy_type',
        'credit_limit',
        'billing_day',
        'payment_due_day',
        'is_tax_invoice_enabled',
        'status',
        'is_active',
        'approved_at',
        'rejected_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'current_point_balance' => 'integer',
            'credit_limit' => 'integer',
            'billing_day' => 'integer',
            'payment_due_day' => 'integer',
            'is_tax_invoice_enabled' => 'boolean',
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
