<?php

namespace App\Services;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
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

        event(new Registered($user));

        return $user->loadMissing('role');
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
        if (! $user->hasVerifiedEmail()) {
            throw new AuthenticationException(
                'Please verify your email address before logging in.',
            );
        }

        $user->loadMissing('role');

        return [
            'user' => $user,
            ...$this->issueToken($user),
        ];
    }

    public function changePassword(User $user, string $newPassword): void
    {
        DB::transaction(function () use ($user, $newPassword): void {
            $user->update([
                'password' => $newPassword,
            ]);

            $user->tokens()->delete();
        });
    }

    public function logout(PersonalAccessToken $accessToken): void
    {
        $sessionAbility = collect($accessToken->abilities)
            ->first(fn(string $ability) => Str::startsWith($ability, 'session:'));

        if ($sessionAbility !== null) {
            PersonalAccessToken::query()
                ->where('tokenable_type', $accessToken->tokenable_type)
                ->where('tokenable_id', $accessToken->tokenable_id)
                ->where('name', 'refresh-token')
                ->whereJsonContains('abilities', $sessionAbility)
                ->delete();
        }

        $accessToken->delete();
    }

    public function refresh(User $user, PersonalAccessToken $refreshToken): array
    {
        $sessionAbility = collect($refreshToken->abilities)
            ->first(fn(string $ability) => Str::startsWith($ability, 'session:'));

        if (
            $refreshToken->name !== 'refresh-token' ||
            ! $refreshToken->can('issue-access-token') ||
            $sessionAbility === null
        ) {
            abort(401, 'Invalid refresh token.');
        }

        return DB::transaction(function () use ($user, $refreshToken, $sessionAbility) {
            PersonalAccessToken::query()
                ->where('tokenable_type', $refreshToken->tokenable_type)
                ->where('tokenable_id', $refreshToken->tokenable_id)
                ->whereJsonContains('abilities', $sessionAbility)
                ->delete();

            return $this->issueToken($user);
        });
    }

    public function sendPasswordResetLink(string $email): string
    {
        return Password::sendResetLink([
            'email' => $email,
        ]);
    }


    public function resetPassword(array $data): string
    {
        return Password::reset(
            [
                'email' => $data['email'],
                'token' => $data['token'],
                'password' => $data['password'],
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                $user->tokens()->delete();
            }
        );
    }

    private function issueToken(User $user): array
    {
        $sessionAbility = 'session:' . Str::uuid();

        $accessTokenExpiresAt = now()->addDay();
        $refreshTokenExpiresAt = now()->addDays(30);

        $accessToken = $user->createToken(
            'access-token',
            ['access-api', $sessionAbility],
            $accessTokenExpiresAt,
        )->plainTextToken;

        $refreshToken = $user->createToken(
            'refresh-token',
            ['issue-access-token', $sessionAbility],
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

    public function updateProfile(User $user, array $data): User
    {
        $user->fill($data);
        $user->save();

        return $user->refresh();
    }

    public function verifyEmail(int $userId, string $hash): bool
    {
        $user = User::query()->findOrFail($userId);

        $expectedHash = sha1($user->getEmailForVerification());

        if (!hash_equals($expectedHash, $hash)) {
            abort(403, 'Invalid email verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return false;
        }

        return $user->markEmailAsVerified();
    }

    public function resendVerificationEmail(string $email): void
    {
        $user = User::query()
            ->where('email', $email)
            ->first();

        if (!$user || $user->hasVerifiedEmail()) {
            return;
        }

        $user->sendEmailVerificationNotification();
    }
}
