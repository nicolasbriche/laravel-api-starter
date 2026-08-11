<?php

use App\Helpers\Logger;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Utiliser notre middleware personnalisé pour l'authentification
        $middleware->redirectGuestsTo(fn () => null);

        // Activer CORS pour les routes API
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        // Alias pour maruer une versio, d'API comme dépréciée
        $middleware->alias([
            'api.deprecated' => \App\Http\Middleware\DeprecatedApiVersion::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Force les réponses JSON pour les erreurs d'authentification
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return $request->is('api/*') || $request->expectsJson();
        });

        // Log toutes les exceptions non gérées (sauf celles qu'on veut ignorer)
        $exceptions->reportable(function (\Throwable $e) {
            if (! $e instanceof ValidationException
                && ! $e instanceof AuthenticationException
                && ! $e instanceof NotFoundHttpException
                && ! $e instanceof ModelNotFoundException) {
                Logger::apiError($e, 'Unhandled exception');
            }
        });

        // 401 - Non authentifié
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }
        });

        // 403 - Non autorisé
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'This action is unauthorized.',
                ], 403);
            }
        });

        // 404 - Route non trouvée OU Modèle non trouvé
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $previous = $e->getPrevious();

                if ($previous instanceof ModelNotFoundException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Resource not found.',
                    ], 404);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint not found.',
                ], 404);
            }
        });

        // 422 - Erreurs de validation
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // Autres erreurs HTTP (429 Too Many Requests, 405 Method Not Allowed, etc.)
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'An error occurred.',
                ], $e->getStatusCode());
            }
        });

        // 500 - Toute autre erreur non gérée
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') && ! app()->hasDebugModeEnabled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An unexpected error occurred.',
                ], 500);
            }
        });
    })->create();
