<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPhoto extends Model
{
    protected $table = 'order_photos';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'photo_type',
        'file_path',
        'sort_order',
        'uploaded_by_user_id',
        'created_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
