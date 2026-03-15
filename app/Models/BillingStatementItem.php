<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingStatementItem extends Model
{
    protected $fillable = [
        'billing_statement_id',
        'point_transaction_id',
        'order_id',
        'amount',
        'summary',
        'description',
    ];

    public function statement()
    {
        return $this->belongsTo(BillingStatement::class, 'billing_statement_id');
    }

    public function pointTransaction()
    {
        return $this->belongsTo(PointTransaction::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
