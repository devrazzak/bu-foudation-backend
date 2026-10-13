<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\IndexUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexUserRequest $request): JsonResponse
    {

        $queryParams = $request->validated();
        $users = $this->userService->listUsers($queryParams);

        return $this->paginatedResponse(
            UserResource::collection($users),
            'Users retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create(
            $request->validated()
        );

        return $this->successResponse(
            new UserResource($user),
            'User created successfully.',
            201
        );
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonResponse
    {
        return $this->successResponse(
            new UserResource($user),
            'User retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update(
            $user,
            $request->validated()
        );

        return $this->successResponse(
            new UserResource($user),
            'User updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);

        return $this->successResponse(
            null,
            'User deleted successfully.'
        );
    }
}
