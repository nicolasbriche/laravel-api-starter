<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *  Marque une version d'API comme dépréciée via les headers HTTP standards (RFC 8594).
 *
 *  Usage dans routes/api.php :
 *      Route::middleware('api.deprecated:2026-06-01,2026-12-01,v2')->groupe(...)
 *
 *  Paramètres :
 *      - $deprecationDate : date (Y-m-d) à laquelle la version est marquée dépréciée
 *      - $sunsetDate      : date (Y-m-d) à laquelle la version sera supprimée
 *      -$seccessor        : version qui remplace celle-ci (ex: 'v2')
 */
class DeprecatedApiVersion
{
    public function handle(
        Request $request,
        Closure $next,
        string $deprecationDate,
        string $sunsetDate,
        ?string $successor = null
    ): Response {
        $response = $next($request);

        $response->headers->set('Deprecation', $deprecationDate);
        $response->headers->set(
            'Sunset',
            \Carbon\Carbon::parse($sunsetDate)->toRfc7231String()
        );

        if ($successor !== null) {
            $response->headers->set(
                'Link',
                sprintf('</api/%s>; rel="successor-version"', $successor)
            );
        }

        return $response;
    }
}
