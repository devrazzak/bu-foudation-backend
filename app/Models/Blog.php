<?php

namespace App\Models;

use App\Enums\BlogStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blog extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'status',
        'short_description',
        'content',
        'image',
        'published_at',
        'meta_title',
        'meta_description'
    ];

    protected $casts = [
        'status' => BlogStatus::class,
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }
}
