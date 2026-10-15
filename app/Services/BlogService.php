<?php

namespace App\Services;

use App\Models\Blog;
use Illuminate\Pagination\LengthAwarePaginator;

class BlogService
{
    /**
     * List all blogs.
     */
    public function listBlogs(array $queryParams = []): LengthAwarePaginator
    {
        $per_page = min($queryParams['per_page'] ?? config('api.pagination.default_per_page'), config('api.pagination.max_per_page'));

        return Blog::query()
            ->with('category')
            ->latest()
            ->when(
                $queryParams['status'] ?? null,
                fn($query, $status) => $query->where('status', $status)
            )
            ->when(
                $queryParams['search'] ?? null,
                fn($query, $search) => $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%");
                })
            )
            ->paginate($per_page);
    }

    /**
     * Create a new blog.
     */
    public function create(array $data): Blog
    {
        return Blog::create($data)->load('category');
    }

    /**
     * Update an existing blog.
     */
    public function update(Blog $blog, array $data): Blog
    {
        $blog->update($data);

        return $blog->refresh()->load('category');
    }

    /**
     * Delete a blog.
     */
    public function delete(Blog $blog): void
    {
        $blog->delete();
    }
}
