<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Routes publiques avec rate limiting
Route::middleware('throttle:api')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
            'service' => 'Laravel API Starter'
        ]);
    });
});

// Routes protégées par Sanctum ET rate limiting
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});