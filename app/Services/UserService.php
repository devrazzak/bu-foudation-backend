<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * List all users.
     */
    public function listUsers(array $queryParams = []): LengthAwarePaginator
    {
        $per_page = min($queryParams['per_page'] ?? config('api.pagination.default_per_page'), config('api.pagination.max_per_page'));

        return User::query()
            ->with('role')
            ->latest()
            ->when(
                $queryParams['status'] ?? null,
                fn($query, $status) => $query->where('status', $status)
            )
            ->when(
                $queryParams['search'] ?? null,
                fn($query, $search) => $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
            )
            ->when(
                $queryParams['sort_by'] ?? null,
                fn($query, $sortBy) => $query->orderBy(
                    $sortBy,
                    $queryParams['sort_dir'] ?? 'desc'
                ),
                fn($query) => $query->latest()
            )
            ->paginate($per_page);
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return User::create($data)->load('role');
    }

    /**
     * Create a new user by an administrator.
     */
    public function createByAdmin(array $data): User
    {
        $data['password'] = bin2hex(random_bytes(32));

        return User::create($data)->load('role');
    }

    /**
     * Update an existing user.
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->load('role')->refresh();
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): void
    {
        $user->delete();
    }
}
