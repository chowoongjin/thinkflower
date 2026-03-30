<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReceiverNotification extends Model
{
    protected $fillable = [
        'order_id',
        'receiver_shop_id',
        'actor_user_id',
        'status',
        'fax_status',
        'kakaotalk_status',
        'fax_file_name',
        'fax_send_key',
        'fax_response',
        'kakaotalk_send_key',
        'kakaotalk_response',
        'error_message',
        'fax_sent_at',
        'kakaotalk_sent_at',
        'history_recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'fax_sent_at' => 'datetime',
            'kakaotalk_sent_at' => 'datetime',
            'history_recorded_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function receiverShop()
    {
        return $this->belongsTo(Shop::class, 'receiver_shop_id');
    }
}
