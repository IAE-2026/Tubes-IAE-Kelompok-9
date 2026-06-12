<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Bypass role checks if request was authenticated via internal API Key (X-IAE-KEY)
        if ($request->attributes->get('auth_method') === 'api_key') {
            return $next($request);
        }

        $user = Auth::user();
        if (! $user) {
            return ApiResponse::error('Unauthorized.', null, 401);
        }

        $userRoles = $user->roles()->pluck('name')->toArray();
        foreach ($roles as $role) {
            if (in_array($role, $userRoles)) {
                return $next($request);
            }
        }

        return ApiResponse::error('Forbidden. Anda tidak memiliki akses ke resource ini.', null, 403);
    }
}
