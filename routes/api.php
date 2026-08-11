<?php

use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Routes publiques avec rate limiting
    Route::middleware('throttle:api')->group(function () {
        Route::get('/health', [HealthController::class, 'check']);
    });

    // Routes protégées par Sanctum ET rate limiting
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::get('/user', [UserController::class, 'me']);
    });
});
