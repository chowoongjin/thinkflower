<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingStatement extends Model
{
    protected $fillable = [
        'shop_id',
        'statement_no',
        'period_start',
        'period_end',
        'debit_total',
        'credit_total',
        'adjust_total',
        'invoice_amount',
        'paid_amount',
        'status',
        'issued_at',
        'due_date',
        'paid_at',
        'tax_invoice_no',
        'memo',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function items()
    {
        return $this->hasMany(BillingStatementItem::class);
    }
}
