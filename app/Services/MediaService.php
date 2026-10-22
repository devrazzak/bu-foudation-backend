<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MediaService
{
    /**
     * List all media.
     */
    public function listMedia($queryParams = []): LengthAwarePaginator
    {
        $per_page = min($queryParams['per_page'] ?? config('api.pagination.default_per_page'), config('api.pagination.max_per_page'));

        return Media::query()
            ->latest()
            ->when(
                $queryParams['search'] ?? null,
                fn ($query, $search) => $query->where(function ($query) use ($search) {
                    $query->where('alt_text', 'like', "%{$search}%")
                        ->orWhere('original_name', 'like', "%{$search}%");
                })
            )
            ->paginate($per_page);
    }

    /**
     * Create a new media.
     */
    public function create(array $data, ?int $uploadedBy = null): Media
    {
        $file = $data['file'];

        $disk = config('media.disk', 'public');
        $path = $file->store('media', $disk);

        try {
            return Media::create([
                'original_name' => $file->getClientOriginalName(),
                'file_name' => basename($path),
                'file_path' => $path,
                'disk' => $disk,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'alt_text' => $data['alt_text'] ?? null,
                'uploaded_by' => $uploadedBy,
            ]);
        } catch (Throwable $exception) {
            Storage::disk($disk)->delete($path);

            throw $exception;
        }
    }

    /**
     * Update an existing media.
     */
    public function update(Media $media, array $data): Media
    {
        $attributes = [];

        if (array_key_exists('alt_text', $data)) {
            $attributes['alt_text'] = $data['alt_text'];
        }

        if (isset($data['file'])) {
            $file = $data['file'];

            $disk = $media->disk;
            $newPath = $file->store('media', $disk);

            $attributes = array_merge($attributes, [
                'original_name' => $file->getClientOriginalName(),
                'file_name' => basename($newPath),
                'file_path' => $newPath,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);

            try {
                $media->update($attributes);
            } catch (Throwable $exception) {
                Storage::disk($disk)->delete($newPath);

                throw $exception;
            }

            // Keep the old file for now. Other records may store its path.
            return $media->refresh();
        }

        $media->update($attributes);

        return $media->refresh();
    }

    /**
     * Delete a media.
     */
    public function delete(Media $media): void
    {
        $disk = $media->disk;
        $path = $media->file_path;

        // Delete the database record first.
        $media->delete();

        // Delete the physical file only when a valid path exists.
        if (is_string($path) && $path !== '') {
            Storage::disk($disk)->delete($path);
        }
    }
}
