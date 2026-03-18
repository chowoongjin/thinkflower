<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'banner_type',
        'title',
        'content',
        'image_path',
        'link_url',
        'sort_order',
        'is_active',
        'start_at',
        'end_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }
}
