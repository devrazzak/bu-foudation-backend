<?php

namespace App\Services;

use App\Models\ActivityCategory;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityCategoryService
{
    /**
     * List all activity categories.
     */
    public function listCategories(array $queryParams = []): LengthAwarePaginator
    {
        $per_page = min($queryParams['per_page'] ?? config('api.pagination.default_per_page'), config('api.pagination.max_per_page'));

        return ActivityCategory::query()
            ->latest()
            ->when(
                $queryParams['status'] ?? null,
                fn($query, $status) => $query->where('status', $status)
            )
            ->when(
                $queryParams['search'] ?? null,
                fn($query, $search) => $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
            )
            ->paginate($per_page);
    }

    /**
     * Create a  new activity category.
     */
    public function create(array $data): ActivityCategory
    {
        return ActivityCategory::create($data);
    }

    /**
     * Update an existing activity category.
     */
    public function update(ActivityCategory $activityCategory, array $data): ActivityCategory
    {
        $activityCategory->update($data);

        return $activityCategory->refresh();
    }

    /**
     * Delete an activity category.
     */
    public function delete(ActivityCategory $activityCategory): void
    {
        $activityCategory->delete();
    }
}
