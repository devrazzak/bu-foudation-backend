<?php

namespace App\Services;

use App\Models\Gallery;
use Illuminate\Pagination\LengthAwarePaginator;

class GalleryService
{
    public function listGalleries(array $queryParams): LengthAwarePaginator
    {
        $per_page = min($queryParams['per_page'] ?? config('api.pagination.default_per_page'), config('api.pagination.max_per_page'));

        return Gallery::query()
            ->latest()
            ->when(
                $queryParams['search'] ?? null,
                fn($query, $search) => $query->where(function ($query) use ($search) {
                    $query->where('alt_text', 'like', "%{$search}%")
                        ->orWhere('original_name', 'like', "%{$search}%");
                })
            )
            ->paginate($per_page);
    }

    /**
     * Create a new gallery.
     */
    public function create(array $data): Gallery
    {
        return Gallery::create($data);
    }

    /**
     * Update an existing gallery.
     */
    public function update(Gallery $gallery, array $data): Gallery
    {
        $gallery->update($data);

        return $gallery->refresh();
    }

    /**
     * Delete a gallery.
     */
    public function delete(Gallery $gallery): void
    {
        $gallery->delete();
    }
}
