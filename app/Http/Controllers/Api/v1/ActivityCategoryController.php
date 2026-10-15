<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivityCategories\IndexActivityCategoryRequest;
use App\Http\Requests\ActivityCategories\StoreActivityCategoryRequest;
use App\Http\Requests\ActivityCategories\UpdateActivityCategoryRequest;
use App\Http\Resources\ActivityCategoryResource;
use App\Models\ActivityCategory;
use App\Services\ActivityCategoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ActivityCategoryController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ActivityCategoryService $categoryService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexActivityCategoryRequest $request): JsonResponse
    {
        $queryParams = $request->validated();

        $activityCategories = $this->categoryService->listCategories($queryParams);

        return $this->paginatedResponse(
            ActivityCategoryResource::collection($activityCategories),
            'Activity categories retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create(
            $request->validated()
        );

        return $this->successResponse(
            new ActivityCategoryResource($category),
            'Activity category created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(ActivityCategory $activityCategory): JsonResponse
    {
        return $this->successResponse(
            new ActivityCategoryResource($activityCategory),
            'Activity category retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityCategoryRequest $request, ActivityCategory $activityCategory): JsonResponse
    {
        $category = $this->categoryService->update(
            $activityCategory,
            $request->validated()
        );

        return $this->successResponse(
            new ActivityCategoryResource($category),
            'Activity category updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActivityCategory $activityCategory): JsonResponse
    {
        if ($activityCategory->activities()->exists()) {
            return $this->errorResponse(
                'Cannot delete this category because activities are associated with it.',
                Response::HTTP_CONFLICT
            );
        }

        $this->categoryService->delete($activityCategory);

        return $this->successResponse(
            null,
            'Activity category deleted successfully'
        );
    }
}
