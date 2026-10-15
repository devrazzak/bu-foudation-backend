<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleService
{
    /**
     * List all roles.
     */
    public function listRoles($queryParams = []): LengthAwarePaginator
    {
        $per_page = min($queryParams['per_page'] ?? config('api.pagination.default_per_page'), config('api.pagination.max_per_page'));

        return Role::query()
            ->latest()
            ->when(
                $queryParams['status'] ?? null,
                fn($query, $status) => $query->where('status', $status)
            )
            ->when(
                $queryParams['search'] ?? null,
                fn($query, $search) => $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
            )
            ->paginate($per_page);
    }

    /**
     * Create a new role.
     */
    public function create(array $data): Role
    {
        return Role::create($data);
    }

    /**
     * Update an existing role.
     */
    public function update(Role $role, array $data): Role
    {
        $role->update($data);

        return $role->refresh();
    }

    /**
     * Delete a role.
     */
    public function delete(Role $role): void
    {
        $role->delete();
    }
}
