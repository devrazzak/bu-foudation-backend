<?php

use App\Http\Controllers\Api\v1\ActivityCategoryController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\RoleController;
use App\Http\Controllers\Api\v1\ActivityController;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\BlogCategoriesController;
use App\Http\Controllers\Api\v1\BlogController;
use App\Http\Controllers\Api\v1\GalleryController;
use App\Http\Controllers\Api\v1\MediaController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/registration', [AuthController::class, 'registration'])->middleware('throttle:5,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Only admin can access these routes
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);
    });

    // Admin and Editor can access these routes
    Route::middleware(['auth:sanctum', 'role:admin,editor'])->group(function () {
        Route::apiResource('activity-categories', ActivityCategoryController::class)
            ->parameter('activity-categories', 'activityCategory');
        Route::apiResource('activities', ActivityController::class);
        Route::apiResource('blog-categories', BlogCategoriesController::class)
            ->parameter('blog-categories', 'blogCategory');
        Route::apiResource('blogs', BlogController::class);
        Route::apiResource('galleries', GalleryController::class);
        Route::apiResource('media', MediaController::class)
            ->parameter('media', 'media');
    });
});
