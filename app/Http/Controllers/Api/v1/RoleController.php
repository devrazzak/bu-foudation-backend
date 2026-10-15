<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Roles\IndexRolesRequest;
use App\Http\Requests\Roles\StoreRoleRequest;
use App\Http\Requests\Roles\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\RoleService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly RoleService $roleService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRolesRequest $request): JsonResponse
    {
        $queryParams = $request->validated();

        $roles = $this->roleService->listRoles($queryParams);

        return $this->paginatedResponse(
            RoleResource::collection($roles),
            'Roles retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->create(
            $request->validated()
        );

        return $this->successResponse(
            new RoleResource($role),
            'Role created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): JsonResponse
    {
        return $this->successResponse(
            new RoleResource($role),
            'Role retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role = $this->roleService->update(
            $role,
            $request->validated()
        );

        return $this->successResponse(
            new RoleResource($role),
            'Role updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $this->roleService->delete($role);

        return $this->successResponse(
            null,
            'Role deleted successfully.'
        );
    }
}
