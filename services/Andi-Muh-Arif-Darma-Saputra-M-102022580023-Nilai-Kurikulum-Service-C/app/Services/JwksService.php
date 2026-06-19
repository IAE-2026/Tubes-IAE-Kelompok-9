<?php

namespace App\Services;

use Firebase\JWT\JWK;
use Illuminate\Support\Facades\Http;

class JwksService
{
    private static ?array $memoryKeySet = null;

    public function getKeySet(): array
    {
        if (self::$memoryKeySet !== null) {
            return self::$memoryKeySet;
        }

        $url = rtrim(config('services.iae.url'), '/').'/api/v1/auth/jwks';
        $response = Http::timeout(10)->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException('Gagal mengambil JWKS IAE SSO.');
        }

        self::$memoryKeySet = JWK::parseKeySet($response->json());

        return self::$memoryKeySet;
    }
}
