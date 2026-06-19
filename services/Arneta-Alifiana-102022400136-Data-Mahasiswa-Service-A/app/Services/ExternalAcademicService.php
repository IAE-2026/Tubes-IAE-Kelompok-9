<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class ExternalAcademicService
{
    public function fetchAcademicSummary(string $nim): array
    {
        $krsConfig = config("iae.services.krs");
        $nilaiConfig = config("iae.services.kurikulum_nilai");

        $krsUrl = $krsConfig['url'] . '/api/v1/krs';
        $nilaiUrl = $nilaiConfig['url'] . '/api/v1/nilai/' . rawurlencode($nim);

        try {
            $responses = Http::pool(fn ($pool) => [
                $pool->as('krs')->timeout(5)->withHeaders([
                    'Accept' => 'application/json',
                    'X-IAE-KEY' => $krsConfig['key'],
                ])->get($krsUrl),
                $pool->as('nilai')->timeout(5)->withHeaders([
                    'Accept' => 'application/json',
                    'X-IAE-KEY' => $nilaiConfig['key'],
                ])->get($nilaiUrl),
            ]);
        } catch (\Exception $e) {
            return [
                'krs' => ['ok' => false, 'message' => 'Gagal menghubungi service eksternal: ' . $e->getMessage(), 'status' => 502],
                'nilai' => ['ok' => false, 'message' => 'Gagal menghubungi service eksternal: ' . $e->getMessage(), 'status' => 502],
            ];
        }

        // Process KRS Response
        $krsResponse = $responses['krs'];
        if ($krsResponse instanceof \Exception || !$krsResponse->successful()) {
            $krsResult = [
                'ok' => false,
                'message' => 'Data KRS tidak dapat diambil dari Service B.',
                'status' => $krsResponse instanceof \Exception ? 502 : $krsResponse->status(),
            ];
        } else {
            $records = collect($krsResponse->json('data') ?? $krsResponse->json() ?? [])
                ->filter(fn (array $item) => (string) Arr::get($item, 'nim') === $nim)
                ->values()
                ->all();
            $krsResult = [
                'ok' => true,
                'data' => $records,
            ];
        }

        // Process Nilai Response
        $nilaiResponse = $responses['nilai'];
        if ($nilaiResponse instanceof \Exception || !$nilaiResponse->successful()) {
            $nilaiResult = [
                'ok' => false,
                'message' => 'Data nilai mahasiswa tidak ditemukan pada Service C.',
                'status' => $nilaiResponse instanceof \Exception ? 502 : $nilaiResponse->status(),
            ];
        } else {
            $nilaiResult = [
                'ok' => true,
                'data' => $nilaiResponse->json('data') ?? $nilaiResponse->json() ?? [],
            ];
        }

        return [
            'krs' => $krsResult,
            'nilai' => $nilaiResult,
        ];
    }

    public function fetchKrsByNim(string $nim): array
    {
        $result = $this->getJson(
            'krs',
            '/api/v1/krs',
            'Data KRS tidak dapat diambil dari Service B.'
        );

        if (! $result['ok']) {
            return $result;
        }

        $records = collect($result['data'])
            ->filter(fn (array $item) => (string) Arr::get($item, 'nim') === $nim)
            ->values()
            ->all();

        return [
            'ok' => true,
            'data' => $records,
        ];
    }

    public function fetchNilaiByNim(string $nim): array
    {
        return $this->getJson(
            'kurikulum_nilai',
            '/api/v1/nilai/'.rawurlencode($nim),
            'Data nilai mahasiswa tidak ditemukan pada Service C.'
        );
    }

    private function getJson(string $service, string $path, string $errorMessage): array
    {
        $config = config("iae.services.$service");
        $url = $config['url'].$path;

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'X-IAE-KEY' => $config['key'],
                ])
                ->get($url);
        } catch (ConnectionException) {
            return $this->failed('Service eksternal tidak dapat dihubungi: '.$url, 502);
        }

        if ($response->status() === 404) {
            return $this->failed($errorMessage, 404);
        }

        if (! $response->successful()) {
            return $this->failed($errorMessage, 502, [
                'service' => $service,
                'status_code' => $response->status(),
            ]);
        }

        return [
            'ok' => true,
            'data' => $response->json('data') ?? $response->json() ?? [],
        ];
    }

    private function failed(string $message, int $status, mixed $errors = null): array
    {
        return [
            'ok' => false,
            'message' => $message,
            'status' => $status,
            'errors' => $errors,
        ];
    }
}
