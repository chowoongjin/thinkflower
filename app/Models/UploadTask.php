<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadTask extends Model
{
    protected $fillable = [
        'task_type',
        'upload_type',
        'status',
        'local_path',
        'original_name',
        'original_mime_type',
        'order_id',
        'shop_id',
        'user_id',
        'photo_field',
        'photo_type',
        'sort_order',
        'disk',
        'mime_type',
        'relative_path',
        'url',
        'error_message',
        'processed_at',
    ];
}
