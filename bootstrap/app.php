
<?php

use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);

        $middleware->redirectGuestsTo(
            fn(Request $request) => $request->is('api/*')
                ? null
                : route('login')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request, Throwable $e): bool =>
            $request->is('api/*') || $request->expectsJson()
        );

        /*
         * Validation errors: HTTP 422
         */
        $exceptions->render(function (
            ValidationException $e,
            Request $request
        ) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'data' => null,
                'errors' => $e->errors(),
            ], 422);
        });

        /*
         * Missing resource or route: HTTP 404
         */
        $exceptions->render(function (
            NotFoundHttpException $e,
            Request $request
        ) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Resource not found.',
                'data' => null,
            ], 404);
        });

        /*
         * Authentication errors: HTTP 401
         */
        $exceptions->render(function (
            AuthenticationException $e,
            Request $request
        ) {
            if (!$request->is('api/*')) {
                return null;
            }

            $message = $e->getMessage() ?: 'Unauthenticated.';

            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
            ], 401);
        });

        /*
         * Other HTTP exceptions
         */
        $exceptions->render(function (
            HttpExceptionInterface $e,
            Request $request
        ) {
            if (!$request->is('api/*')) {
                return null;
            }

            $status = $e->getStatusCode();

            $message = match ($status) {
                401 => 'Unauthenticated.',
                403 => 'Forbidden.',
                404 => 'Resource not found.',
                405 => 'Method not allowed.',
                419 => 'Request expired.',
                422 => 'Validation failed.',
                429 => 'Too many requests. Please try again later.',
                default => $status >= 500
                    ? 'Internal server error.'
                    : 'Request failed.',
            };

            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
            ], $status);
        });

        /*
         * Unexpected exceptions: HTTP 500
         *
         * Laravel handles exception reporting.
         * Never expose internal exception details to API clients.
         */
        $exceptions->render(function (
            Throwable $e,
            Request $request
        ) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Internal server error.',
                'data' => null,
            ], 500);
        });
    })
    ->create();
