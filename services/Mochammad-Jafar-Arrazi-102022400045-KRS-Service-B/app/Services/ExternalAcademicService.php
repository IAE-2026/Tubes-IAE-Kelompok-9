<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ExternalAcademicService
{
    public function validateKrsRequest(array $input): array
    {
        if (! config('iae.external_validation_enabled', true)) {
            return [
                'ok' => true,
                'mahasiswa' => [],
                'nilai' => [],
                'kurikulum' => [],
            ];
        }

        $mahasiswa = $this->fetchMahasiswa($input['nim']);
        if (! $mahasiswa['ok']) {
            return $mahasiswa;
        }

        if (! $this->isMahasiswaAktif($mahasiswa['data'])) {
            return $this->failed('Mahasiswa tidak aktif sehingga tidak dapat melakukan KRS.', 422);
        }

        $nilai = $this->fetchNilai($input['nim']);
        if (! $nilai['ok']) {
            return $nilai;
        }

        if (! $this->isIpsMemenuhiSyarat($nilai['data'])) {
            return $this->failed(
                'IPS semester lalu tidak memenuhi syarat minimum '.number_format(config('iae.minimum_ips', 2.00), 2).'.',
                422
            );
        }

        $kurikulum = $this->fetchKurikulum($input['kode_mata_kuliah']);
        if (! $kurikulum['ok']) {
            return $kurikulum;
        }

        return [
            'ok' => true,
            'mahasiswa' => $mahasiswa['data'],
            'nilai' => $nilai['data'],
            'kurikulum' => $kurikulum['data'],
        ];
    }

    private function fetchMahasiswa(string $nim): array
    {
        return $this->getJson(
            'mahasiswa',
            '/api/v1/mahasiswa/'.rawurlencode($nim),
            'Data mahasiswa tidak ditemukan atau tidak dapat divalidasi.'
        );
    }

    private function fetchNilai(string $nim): array
    {
        return $this->getJson(
            'kurikulum_nilai',
            '/api/v1/nilai/'.rawurlencode($nim),
            'Data nilai mahasiswa tidak ditemukan atau tidak dapat divalidasi.'
        );
    }

    private function fetchKurikulum(string $kode): array
    {
        return $this->getJson(
            'kurikulum_nilai',
            '/api/v1/kurikulum/'.rawurlencode($kode),
            'Mata kuliah tidak ditemukan pada kurikulum.'
        );
    }

    private function getJson(string $service, string $path, string $notFoundMessage): array
    {
        $config = config("iae.services.$service");
        $url = $config['url'].$path;

        $headers = ['Accept' => 'application/json'];
        if ($service === 'mahasiswa') {
            $headers['X-API-KEY'] = $config['key'];
        } else {
            $headers['X-IAE-KEY'] = $config['key'];
        }

        try {
            $response = Http::timeout(5)
                ->withHeaders($headers)
                ->get($url);
        } catch (ConnectionException) {
            return $this->failed('Service eksternal tidak dapat dihubungi: '.$url, 502);
        }

        if ($response->status() === 404) {
            return $this->failed($notFoundMessage, 422);
        }

        if (! $response->successful()) {
            return $this->failed('Service eksternal gagal memproses validasi.', 502, [
                'service' => $service,
                'status_code' => $response->status(),
            ]);
        }

        return [
            'ok' => true,
            'data' => $response->json('data') ?? $response->json() ?? [],
        ];
    }

    private function isMahasiswaAktif(array $data): bool
    {
        if (Arr::get($data, 'is_aktif') === true) {
            return true;
        }

        $status = Str::lower((string) (
            Arr::get($data, 'status')
            ?? Arr::get($data, 'status_mahasiswa')
            ?? Arr::get($data, 'status_akademik')
            ?? ''
        ));

        if ($status === '') {
            return true;
        }

        return in_array($status, ['aktif', 'active'], true);
    }

    private function isIpsMemenuhiSyarat(array $data): bool
    {
        $ips = Arr::get($data, 'ips')
            ?? Arr::get($data, 'ips_semester_lalu')
            ?? Arr::get($data, 'ip_semester')
            ?? null;

        if ($ips === null) {
            return true;
        }

        return (float) $ips >= (float) config('iae.minimum_ips', 2.00);
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
