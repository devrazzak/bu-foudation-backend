<?php

namespace App\Services;

use App\Enums\UserStatus;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct(readonly private UserService $userService) {}

    public function register(array $data): array
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'profile_picture' => $data['profile_picture'] ?? null,
            'status' => UserStatus::Active,
            'terms_accepted' => $data['terms_accepted'] ?? true,
            'password' => Hash::make($data['password']),
        ];
        $user = $this->userService->create($userData);

        return [
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ];
    }
}
