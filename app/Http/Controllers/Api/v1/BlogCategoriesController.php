<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogCategories\IndexBlogCategoriesRequest;
use App\Http\Requests\BlogCategories\StoreBlogCategoriesRequest;
use App\Http\Requests\BlogCategories\UpdateBlogCategoriesRequest;
use App\Http\Resources\BlogCategoryResource;
use App\Models\BlogCategory;
use App\Services\BlogCategoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BlogCategoriesController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly BlogCategoryService $blogCategoryService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(IndexBlogCategoriesRequest $request): JsonResponse
    {
        $queryParams = $request->validated();

        $categories = $this->blogCategoryService->listCategories($queryParams);

        return $this->paginatedResponse(
            BlogCategoryResource::collection($categories),
            'Blog categories retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogCategoriesRequest $request): JsonResponse
    {
        $category = $this->blogCategoryService->create(
            $request->validated()
        );

        return $this->successResponse(
            new BlogCategoryResource($category),
            'Blog category created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogCategory $blogCategory)
    {
        return $this->successResponse(
            new BlogCategoryResource($blogCategory),
            'Blog category retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogCategoriesRequest $request, BlogCategory $blogCategory)
    {
        $category = $this->blogCategoryService->update(
            $blogCategory,
            $request->validated()
        );

        return $this->successResponse(
            new BlogCategoryResource($category),
            'Blog category updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogCategory $blogCategory)
    {
        if ($blogCategory->blogs()->exists()) {
            return $this->errorResponse(
                'Cannot delete this category because activities are associated with it.',
                Response::HTTP_CONFLICT
            );
        }

        $this->blogCategoryService->delete($blogCategory);

        return $this->successResponse(
            null,
            'Blog category deleted successfully.'
        );
    }
}
