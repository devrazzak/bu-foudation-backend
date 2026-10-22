<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\IndexMediaRequest;
use App\Http\Requests\Media\StoreMediaRequest;
use App\Http\Requests\Media\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Services\MediaService;
use App\Traits\ApiResponse;

class MediaController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly MediaService $mediaService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexMediaRequest $request)
    {
        $queryParams = $request->validated();

        $media = $this->mediaService->listMedia($queryParams);

        return $this->successResponse(
            MediaResource::collection($media),
            'Media retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMediaRequest $request)
    {
        $media = $this->mediaService->create(
            $request->validated(),
            (int) $request->user()->id,
        );

        return $this->successResponse(
            new MediaResource($media),
            'Media uploaded successfully.'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media)
    {
        return $this->successResponse(
            new MediaResource($media),
            'Media retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMediaRequest $request, Media $media)
    {
        $media = $this->mediaService->update($media, $request->validated());

        return $this->successResponse(
            new MediaResource($media),
            'Media updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media)
    {
        $this->mediaService->delete($media);

        return $this->successResponse(
            null,
            'Media deleted successfully.'
        );
    }
}
