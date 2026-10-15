<?php

namespace App\Services;

use App\Models\BlogCategory;
use Illuminate\Pagination\LengthAwarePaginator;

class BlogCategoryService
{
    /**
     * List all blog categories.
     */
    public function listCategories(array $queryParams = []): LengthAwarePaginator
    {
        $per_page = min($queryParams['per_page'] ?? config('api.pagination.default_per_page'), config('api.pagination.max_per_page'));

        return BlogCategory::query()
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
     * Create a new blog category.
     */
    public function create(array $data): BlogCategory
    {
        return BlogCategory::create($data);
    }

    /**
     * Update an existing blog category.
     */
    public function update(BlogCategory $blogCategory, array $data): BlogCategory
    {
        $blogCategory->update($data);

        return $blogCategory->refresh();
    }

    /**
     * Delete a blog category.
     */
    public function delete(BlogCategory $blogCategory): void
    {
        $blogCategory->delete();
    }
}
