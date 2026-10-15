<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityService
{
    /**
     * List all activities.
     */
    public function listActivities(array $queryParams = []): LengthAwarePaginator
    {
        $per_page = min($queryParams['per_page'] ?? config('api.pagination.default_per_page'), config('api.pagination.max_per_page'));

        return Activity::query()
            ->with('category', 'user')
            ->latest()
            ->when(
                $queryParams['status'] ?? null,
                fn($query, $status) => $query->where('status', $status)
            )
            ->when(
                $queryParams['search'] ?? null,
                fn($query, $search) => $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%");
                })
            )
            ->paginate($per_page);
    }

    /**
     * Create a new activity.
     */
    public function create(array $data): Activity
    {
        // $data['created_by'] = $userId;
        return Activity::create($data)->load('category', 'user');
    }

    /**
     * Update an existing activity.
     */
    public function update(Activity $activity, array $data): Activity
    {
        $activity->update($data);

        return $activity->refresh()->load('category', 'user');
    }

    /**
     * Delete an activity.
     */
    public function delete(Activity $activity): void
    {
        $activity->delete();
    }
}
