<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

class HealthController extends BaseController
{
    /**
     * Health check endpoint
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
