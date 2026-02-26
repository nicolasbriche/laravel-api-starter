<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class Logger
{
	/**
	 * Log an API request
	 */
	public static function apiRequest(string $method, string $endpoint, ?int $userId = null): void
	{
		Log::info('API Request', [
			'method' => $method,
			'endpoint' => $endpoint,
			'user_id' => $userId,
			'ip' => request()->ip(),
			'user_agent' => request()->userAgent(),
		]);
	}

	/**
	 * Log an API error
	 */
	public static function apiError(\Throwable $exception, string $context = ''): void
	{
		Log::error('API Error', [
			'context' => $context,
			'message' => $exception->getMessage(),
			'file' => $exception->getFile(),
			'line' => $exception->getLine(),
			'trace' => $exception->getTraceAsString(),
		]);
	}
}