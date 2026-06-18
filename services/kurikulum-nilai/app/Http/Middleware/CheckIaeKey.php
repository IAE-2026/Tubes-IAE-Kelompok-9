<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIaeKey
{
    /**
     * Memvalidasi header X-IAE-KEY pada setiap request API.
     * Value yang valid: API Key MHS (KEY-MHS-117).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-IAE-KEY');

        if (!$apiKey || $apiKey !== 'KEY-MHS-117') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Header X-IAE-KEY tidak valid atau tidak ditemukan.',
                'errors' => null,
            ], 401);
        }

        return $next($request);
    }
}
