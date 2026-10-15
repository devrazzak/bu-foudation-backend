<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\IndexBlogRequest;
use App\Http\Requests\Blog\StoreBlogRequest;
use App\Http\Requests\Blog\UpdateBlogRequest;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Services\BlogService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{

    use ApiResponse;

    public function __construct(private readonly BlogService $blogService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(IndexBlogRequest $request)
    {

        $queryParams = $request->validated();

        $blogs = $this->blogService->listBlogs($queryParams);

        return $this->paginatedResponse(
            BlogResource::collection($blogs),
            'Blogs retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogRequest $request)
    {
        $blog = $this->blogService->create(
            $request->validated()
        );

        return $this->successResponse(
            new BlogResource($blog),
            'Blog created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        return $this->successResponse(
            new BlogResource($blog->load('category')),
            'Blog retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        $blog = $this->blogService->update(
            $blog,
            $request->validated()
        );

        return $this->successResponse(
            new BlogResource($blog),
            'Blog updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        $this->blogService->delete($blog);

        return $this->successResponse(
            null,
            'Blog deleted successfully.'
        );
    }
}
