<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIaeApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = (string) config('iae.api_key');
        $providedKey = (string) $request->header('X-IAE-KEY', '');

        if ($providedKey === '' || ! hash_equals($expectedKey, $providedKey)) {
            return ApiResponse::error('API key tidak valid atau tidak dikirim.', null, 401);
        }

        return $next($request);
    }
}
