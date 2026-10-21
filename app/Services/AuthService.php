<?php

namespace App\Services;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use LogicException;

class AuthService
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    /**
     * Register a new user with the default "user" role.
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $defaultRole = Role::query()
                ->where('name', 'user')
                ->first();

            if (!$defaultRole) {
                throw new LogicException(
                    'The default "user" role does not exist. Please seed the roles table.',
                );
            }

            $user = $this->userService->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role_id' => $defaultRole->id,
                'phone_number' => $data['phone_number'] ?? null,
                'terms_accepted' => $data['terms_accepted'] ?? false,
                'status' => UserStatus::Active->value,
            ]);

            return $user->loadMissing('role');
        });
    }

    public function login(array $data): array
    {
        $user = User::query()->where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new AuthenticationException('Invalid email or password.');
        }

        if ($user->status !== UserStatus::Active) {
            throw new AuthenticationException('Your account is inactive.');
        }

        $user->loadMissing('role');

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            ...$this->issueToken($user),
        ];
    }

    private function issueToken(User $user): array
    {
        $accessTokenExpiresAt = now()->addDay();
        $refreshTokenExpiresAt = now()->addDays(30);

        $accessToken = $user->createToken(
            'access-token',
            ['access-api'],
            $accessTokenExpiresAt,
        )->plainTextToken;

        $refreshToken = $user->createToken(
            'refresh-token',
            ['issue-access-token'],
            $refreshTokenExpiresAt,
        )->plainTextToken;

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 86400,
            'refresh_token_expires_at' => $refreshTokenExpiresAt->toISOString(),
        ];
    }
}
