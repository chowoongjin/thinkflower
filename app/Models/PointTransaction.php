<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    protected $fillable = [
        'shop_id',
        'user_id',
        'order_id',
        'payment_transaction_id',
        'transaction_no',
        'transaction_type',
        'direction',
        'amount',
        'balance_before',
        'balance_after',
        'summary',
        'description',
        'transacted_at',
    ];

    protected function casts(): array
    {
        return [
            'transacted_at' => 'datetime',
        ];
    }
}
