<?php

namespace App\Models;

use App\Enums\ActivityCategoryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => ActivityCategoryStatus::class,
    ];

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'category_id');
    }
}
