<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Activities\IndexActivityRequest;
use App\Http\Requests\Activities\StoreActivityRequest;
use App\Http\Requests\Activities\UpdateActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Services\ActivityService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ActivityService $activityService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexActivityRequest $request): JsonResponse
    {
        $queryParams = $request->validated();

        $activities = $this->activityService->listActivities($queryParams);

        return $this->paginatedResponse(
            ActivityResource::collection($activities),
            'Activities retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRequest $request): JsonResponse
    {
        $activity = $this->activityService->create(
            $request->validated()
            // (int) $request->user()->id
        );

        return $this->successResponse(
            new ActivityResource($activity),
            'Activity created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        return $this->successResponse(
            new ActivityResource($activity->load('category')),
            'Activity retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityRequest $request, Activity $activity): JsonResponse
    {
        $activity = $this->activityService->update(
            $activity,
            $request->validated()
        );

        return $this->successResponse(
            new ActivityResource($activity),
            'Activity updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity): JsonResponse
    {
        $this->activityService->delete($activity);

        return $this->successResponse(
            null,
            'Activity deleted successfully.'
        );
    }
}
