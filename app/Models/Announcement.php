<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'is_popup',
        'is_active',
        'start_at',
        'end_at',
        'view_count',
        'recommend_count',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'is_popup' => 'boolean',
            'is_active' => 'boolean',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'view_count' => 'integer',
            'recommend_count' => 'integer',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }
}
