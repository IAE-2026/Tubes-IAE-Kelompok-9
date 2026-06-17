<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $expectedKey = (string) config('app.api_key');
        $apiKey = trim((string) $request->header('X-API-KEY', ''));
        $iaeKey = trim((string) $request->header('X-IAE-KEY', ''));

        if ($apiKey === '') {
            $apiKey = trim((string) $request->query('api_key', ''));
        }
        if ($iaeKey === '') {
            $iaeKey = trim((string) $request->query('iae_key', ''));
        }

        $isValid = ($apiKey !== '' && hash_equals($expectedKey, $apiKey))
            || ($iaeKey !== '' && hash_equals($expectedKey, $iaeKey));

        if (! $isValid) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid or missing API Key (X-API-KEY or X-IAE-KEY).',
                'data' => null,
            ], 401);
        }

        return $next($request);
    }
}
