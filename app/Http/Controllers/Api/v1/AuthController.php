<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Http\Requests\Auth\ResendVerificationEmailRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AuthService $authService,
    ) {}

    /**
     * Register a new user
     */
    public function registration(RegistrationRequest $request): JsonResponse
    {
        $user = $this->authService->register(
            $request->validated(),
        );

        return $this->successResponse(
            new UserResource($user),
            'User registered successfully',
            201,
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return $this->successResponse(
            [
                'user' => new UserResource($result['user']),
                'access_token' => $result['access_token'],
                'refresh_token' => $result['refresh_token'],
                'token_type' => $result['token_type'],
                'expires_in' => $result['expires_in'],
                'refresh_token_expires_at' => $result['refresh_token_expires_at'],
            ],
            'Login successful',
        );
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->successResponse(
            new UserResource($user),
            'User details retrieved successfully',
        );
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {

        $this->authService->changePassword(
            $request->user(),
            $request->validated('password'),
        );

        return $this->successResponse(
            null,
            'Password changed successfully',
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $accessToken = $request->user()->currentAccessToken();

        if (! $accessToken instanceof PersonalAccessToken) {
            return $this->errorResponse(
                'Invalid access token.',
                401,
            );
        }

        $this->authService->logout($accessToken);

        return $this->successResponse(
            null,
            'Logged out successfully.',
        );
    }

    public function refresh(Request $request): JsonResponse
    {
        $refreshToken = $request->user()->currentAccessToken();

        if (! $refreshToken instanceof PersonalAccessToken) {
            return $this->errorResponse(
                'Invalid refresh token.',
                401,
            );
        }

        $tokens = $this->authService->refresh(
            $request->user(),
            $refreshToken,
        );

        return $this->successResponse(
            $tokens,
            'Token refreshed successfully.',
        );
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->sendPasswordResetLink(
            $request->validated('email'),
        );

        if ($status === Password::RESET_LINK_SENT) {
            return $this->successResponse(
                null,
                'If the email is registered, a password reset link will be sent.',
            );
        }

        return $this->successResponse(
            null,
            'If the email is registered, a password reset link will be sent.',
        );
    }


    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = $this->authService->resetPassword(
            $request->validated()
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->successResponse(
                null,
                'Password has been reset successfully. Please log in again.',
            );
        }

        $message = match ($status) {
            Password::INVALID_TOKEN => 'Password reset token is invalid or has expired.',
            Password::INVALID_USER => 'No user found with the provided email address.',
            default => 'Failed to reset password. Please try again.',
        };

        return $this->errorResponse($message, 400);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->authService->updateProfile(
            $request->user(),
            $request->validated(),
        );

        return $this->successResponse(
            new UserResource($user),
            'Profile updated successfully.',
        );
    }

    public function verifyEmail(string $id, string $hash)
    {
        $verified = $this->authService->verifyEmail(
            (int) $id,
            $hash,
        );

        $status = $verified ? 'success' : 'already-verified';

        return redirect()->away(
            config('app.frontend_url') . '/email-verified?status=' . $status,
        );
    }

    public function resendVerificationEmail(ResendVerificationEmailRequest $request): JsonResponse
    {
        $this->authService->resendVerificationEmail(
            $request->validated('email'),
        );

        return $this->successResponse(
            null,
            'If the account exists and is not verified, a verification email will be sent.',
        );
    }
}
