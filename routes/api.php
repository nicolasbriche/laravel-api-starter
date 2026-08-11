<?php

use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Les routes sont versionnées par préfixe d'URI : /api/v1, /api/v2, etc.
|
| POUR CRÉER UNE NOUVELLE VERSION (v2) :
|   1. Créer app/Http/Controllers/Api/V2/ (et Resources/Requests si le format change)
|   2. Ajouter un groupe Route::prefix('v2') ci-dessous
|   3. Marquer la v1 comme dépréciée en ajoutant le middleware :
|        'api.deprecated:DATE_DEPRECATION,DATE_SUPPRESSION,v2'
|      exemple : 'api.deprecated:2026-06-01,2026-12-01,v2'
|   4. Supprimer le groupe v1 à la date de sunset annoncée
|
| NE PAS dupliquer entre versions : BaseController, BaseRequest, Models, Services.
|
*/

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
