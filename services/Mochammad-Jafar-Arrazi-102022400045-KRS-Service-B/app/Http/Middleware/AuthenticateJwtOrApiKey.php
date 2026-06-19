<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Models\User;
use App\Support\ApiResponse;
use Closure;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AuthenticateJwtOrApiKey
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Check for X-IAE-KEY header first (backward compatibility for service-to-service calls)
        $expectedKey = (string) config('iae.api_key');
        $providedKey = (string) $request->header('X-IAE-KEY', '');

        if ($providedKey !== '' && hash_equals($expectedKey, $providedKey)) {
            $request->attributes->set('auth_method', 'api_key');

            return $next($request);
        }

        // 2. Check for Authorization: Bearer <token>
        $token = $request->bearerToken();
        if (! $token) {
            return ApiResponse::error('API key tidak valid atau tidak dikirim.', null, 401);
        }

        try {
            // Retrieve and cache JWKS keys from Central SSO
            $jwks = Cache::remember('iae_jwks', 3600, function () {
                $url = rtrim(config('iae.sso.url', 'https://iae-sso.virtualfri.id'), '/').'/api/v1/auth/jwks';
                $response = Http::get($url);
                if ($response->successful()) {
                    return $response->json();
                }
                throw new \Exception('Gagal mengambil JWKS dari central SSO.');
            });

            // Decode the JWT token
            $decoded = JWT::decode($token, JWK::parseKeySet($jwks));

            // Extract user profile from token
            if (! isset($decoded->profile) || ! isset($decoded->profile->email)) {
                return ApiResponse::error('Unauthorized. Token tidak valid (profile tidak lengkap).', null, 401);
            }

            $profile = $decoded->profile;
            $email = $profile->email;
            $name = $profile->name ?? 'SSO User';
            $nim = $profile->nim ?? null;

            // Find or create local User
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => bcrypt(Str::random(16)),
                ]
            );

            // Determine role: if they have a nim in JWT, they are mahasiswa. Else dosen.
            $roleName = $nim ? 'mahasiswa' : 'dosen';
            $role = Role::firstOrCreate(['name' => $roleName]);

            if (! $user->roles()->where('role_id', $role->id)->exists()) {
                $user->roles()->attach($role->id);
            }

            // Authenticate user in Laravel session/guard
            Auth::login($user);

            // Attach user roles and profile info to the request for easy access in controllers
            $request->attributes->set('auth_method', 'jwt');
            $request->attributes->set('sso_profile', $profile);
            $request->attributes->set('user_roles', $user->roles()->pluck('name')->toArray());

            return $next($request);

        } catch (\Exception $e) {
            return ApiResponse::error('Unauthorized. Token tidak valid atau kedaluwarsa: '.$e->getMessage(), null, 401);
        }
    }
}
