<?php

namespace App\Models;

use App\Enums\BlogCategoryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => BlogCategoryStatus::class,
    ];

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class, 'category_id');
    }
}
