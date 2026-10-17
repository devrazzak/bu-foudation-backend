<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\galleries\IndexGalleryRequest;
use App\Http\Requests\galleries\StoreGalleryRequest;
use App\Http\Requests\galleries\UpdateGalleryRequest;
use App\Http\Resources\GalleryResource;
use App\Models\Gallery;
use App\Services\GalleryService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected readonly GalleryService $galleryService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexGalleryRequest $request): JsonResponse
    {
        $queryParams = $request->validated();

        $galleries = $this->galleryService->listGalleries($queryParams);

        return $this->paginatedResponse(
            GalleryResource::collection($galleries),
            'Galleries retrieved successfully.',
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGalleryRequest $request): JsonResponse
    {
        $gallery = $this->galleryService->create(
            $request->validated()
        );

        return $this->successResponse(
            new GalleryResource($gallery),
            'Gallery created successfully.'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Gallery $gallery): JsonResponse
    {
        return $this->successResponse(
            new GalleryResource($gallery),
            'Gallery retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGalleryRequest $request, Gallery $gallery): JsonResponse
    {
        $gallery = $this->galleryService->update(
            $gallery,
            $request->validated()
        );

        return $this->successResponse(
            new GalleryResource($gallery),
            'Gallery updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gallery $gallery): JsonResponse
    {
        $this->galleryService->delete($gallery);

        return $this->successResponse(
            null,
            'Gallery deleted successfully.'
        );
    }
}
