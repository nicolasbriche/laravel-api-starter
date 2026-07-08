<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

class HealthController extends BaseController
{
    /**
     * Health check
     *
     * Retourne le statut de santé de l'API. utile pour les systèmes de monitoring
     * et les load balancers afin de vérifier que le service est opérationnel.
     */
    public function check(): JsonResponse
    {
        return $this->success([
            'status' => 'healthy',
            'timestamp' => now(),
            'service' => config('app.name'),
            'environment' => config('app.env'),
        ], 'API is running');
    }
}
