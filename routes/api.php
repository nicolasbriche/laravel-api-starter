<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HealthController;
use App\Http\Resources\UserResource;

// Routes publiques avec rate limiting
Route::middleware('throttle:api')->group(function () {
    Route::get('/health', [HealthController::class, 'check']);
});

// Routes protégées par Sanctum ET rate limiting
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('/user', function (Request $request) {
        return new UserResource($request->user());
    });
});