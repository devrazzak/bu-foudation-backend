<?php

namespace App\Models;

use App\Enums\ActivityStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'short_description',
        'content',
        'status',
        'cover_image',
        'location',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'status' => ActivityStatus::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ActivityCategory::class, 'category_id');
    }
}
