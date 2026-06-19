<?php

namespace App\Services;

use App\Support\EnvWriter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IaeTokenService
{
    private const REFRESH_BUFFER_SECONDS = 300;

    private static ?string $memoryToken = null;

    private static ?string $memoryExpiresAt = null;

    public function getValidToken(bool $persistToEnv = false): string
    {
        $token = self::$memoryToken ?? config('services.iae.token');
        $expiresAt = self::$memoryExpiresAt ?? config('services.iae.token_expires_at');

        if ($token && $expiresAt && ! $this->isExpired($expiresAt)) {
            return $token;
        }

        return $this->refreshToken($persistToEnv);
    }

    public function refreshToken(bool $persistToEnv = true): string
    {
        $response = $this->requestToken();

        if (! $response->successful()) {
            throw new \RuntimeException(
                'Gagal mengambil token IAE SSO: '.$response->body()
            );
        }

        $payload = $response->json();
        $token = $payload['token'] ?? null;

        if (! $token) {
            throw new \RuntimeException('Response token IAE SSO tidak valid.');
        }

        $expiresIn = (int) ($payload['expires_in'] ?? 3600);
        $expiresAt = Carbon::now()->addSeconds($expiresIn)->toIso8601String();

        if ($persistToEnv) {
            EnvWriter::set('IAE_SSO_TOKEN', $token);
            EnvWriter::set('IAE_SSO_TOKEN_EXPIRES_AT', $expiresAt);
        }

        putenv("IAE_SSO_TOKEN={$token}");
        putenv("IAE_SSO_TOKEN_EXPIRES_AT={$expiresAt}");
        $_ENV['IAE_SSO_TOKEN'] = $token;
        $_ENV['IAE_SSO_TOKEN_EXPIRES_AT'] = $expiresAt;
        self::$memoryToken = $token;
        self::$memoryExpiresAt = $expiresAt;

        Log::info('IAE SSO token refreshed', [
            'token_type' => $payload['token_type'] ?? 'unknown',
            'expires_at' => $expiresAt,
        ]);

        return $token;
    }

    private function requestToken()
    {
        $baseUrl = rtrim(config('services.iae.url'), '/');
        $apiKey = config('services.iae.api_key');
        $email = config('services.iae.warga_email');
        $password = config('services.iae.warga_password');

        if ($apiKey) {
            $nim = config('services.iae.owner_nim');
            $payload = ['api_key' => $apiKey];

            if ($nim) {
                $payload['nim'] = $nim;
            }

            $response = Http::timeout(15)->post("{$baseUrl}/api/v1/auth/token", $payload);

            if ($response->successful()) {
                return $response;
            }

            Log::warning('IAE M2M token gagal, mencoba fallback kredensial warga.', [
                'status' => $response->status(),
            ]);
        }

        if ($email && $password) {
            return Http::timeout(15)->post("{$baseUrl}/api/v1/auth/token", [
                'email' => $email,
                'password' => $password,
            ]);
        }

        throw new \RuntimeException('IAE_API_KEY atau kredensial warga belum dikonfigurasi.');
    }

    private function isExpired(string $expiresAt): bool
    {
        try {
            return Carbon::parse($expiresAt)->subSeconds(self::REFRESH_BUFFER_SECONDS)->isPast();
        } catch (\Throwable) {
            return true;
        }
    }
}
