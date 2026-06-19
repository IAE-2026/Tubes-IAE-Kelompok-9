<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostNilaiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_nilai_requires_bearer_jwt(): void
    {
        $response = $this->withHeaders([
            'X-IAE-KEY' => 'KEY-MHS-117',
        ])->postJson('/api/v1/nilai', [
            'nim' => '209907170001',
            'kode_matkul' => 'SI101',
            'nama_matkul' => 'Algoritma dan Pemrograman',
            'nilai_huruf' => 'A',
            'nilai_angka' => 4,
            'sks' => 3,
            'semester' => 1,
            'tahun_ajaran' => '2025/2026',
        ]);

        $response->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthorized. Bearer JWT dari IAE SSO diperlukan.');
    }

    public function test_post_nilai_rejects_invalid_x_iae_key(): void
    {
        $response = $this->withHeaders([
            'X-IAE-KEY' => '102022580023',
            'Authorization' => 'Bearer fake.jwt.token',
        ])->postJson('/api/v1/nilai', [
            'nim' => '209907170001',
            'kode_matkul' => 'SI101',
            'nama_matkul' => 'Algoritma dan Pemrograman',
            'nilai_huruf' => 'A',
            'nilai_angka' => 4,
            'sks' => 3,
            'semester' => 1,
            'tahun_ajaran' => '2025/2026',
        ]);

        $response->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthorized. Header X-IAE-KEY tidak valid atau tidak ditemukan.');
    }
}
