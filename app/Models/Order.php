<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Shop;
use App\Models\OrderHistory;
use App\Models\OrderPhoto;


class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_no',
        'order_type',
        'brokerage_type',
        'orderer_shop_id',
        'receiver_shop_id',
        'assigned_by_admin_user_id',
        'created_user_id',
        'created_channel',
        'created_by_admin_user_id',
        'product_category_id',
        'product_name',
        'product_detail',
        'product_image_path',
        'product_image_url',
        'original_amount',
        'order_amount',
        'supply_amount',
        'brokerage_fee',
        'payment_amount',
        'point_used_amount',
        'point_earned_amount',
        'delivery_zipcode',
        'delivery_addr1',
        'delivery_addr2',
        'delivery_place',
        'delivery_date',
        'delivery_hour',
        'delivery_minute',
        'delivery_time_type',
        'is_hidden',
        'hidden_at',
        'hidden_by_admin_user_id',
        'is_urgent',
        'recipient_name',
        'recipient_phone',
        'ribbon_phrase',
        'sender_name',
        'card_message',
        'request_note',
        'request_photo',
        'current_status',
        'accepted_at',
        'accepted_by_type',
        'delivered_at',
        'cancelled_at',
        'cancel_reason',
        'receiver_name',
        'receiver_relation',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'is_urgent' => 'boolean',
            'is_hidden' => 'boolean',
            'hidden_at' => 'datetime',
            'request_photo' => 'boolean',
            'accepted_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class);
    }
    public function ordererShop()
    {
        return $this->belongsTo(Shop::class, 'orderer_shop_id');
    }

    public function receiverShop()
    {
        return $this->belongsTo(Shop::class, 'receiver_shop_id');
    }
    public function getRouteKeyName(): string
    {
        return 'order_no';
    }
    public function photos()
    {
        return $this->hasMany(OrderPhoto::class);
    }

    public function uploadedPhotos()
    {
        return $this->hasMany(OrderPhoto::class)
            ->whereNotNull('file_path')
            ->where('file_path', '!=', '');
    }

    public function receiverNotifications()
    {
        return $this->hasMany(OrderReceiverNotification::class);
    }
}
