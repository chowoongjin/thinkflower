<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'shop_id',
        'user_id',
        'payment_no',
        'pg_provider',
        'pg_transaction_id',
        'payment_method',
        'payment_amount',
        'charged_point_amount',
        'bonus_point_amount',
        'status',
        'paid_at',
        'cancelled_at',
        'title',
        'note',
        'provider',
        'payment_key',
        'order_id',
        'order_name',
        'method',
        'total_amount',
        'balance_amount',
        'supplied_amount',
        'vat',
        'requested_at',
        'approved_at',
        'raw_response',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'requested_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }
}
