<?php

use App\Http\Controllers\Api\v1\ActivityCategoryController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\RoleController;
use App\Http\Controllers\Api\v1\ActivityController;
use App\Http\Controllers\Api\v1\BlogCategoriesController;
use App\Http\Controllers\Api\v1\BlogController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('activity-categories', ActivityCategoryController::class)
        ->parameter('activity-categories', 'activityCategory');
    Route::apiResource('activities', ActivityController::class);
    Route::apiResource('blog-categories', BlogCategoriesController::class)
        ->parameter('blog-categories', 'blogCategory');
    Route::apiResource('blogs', BlogController::class);
});
