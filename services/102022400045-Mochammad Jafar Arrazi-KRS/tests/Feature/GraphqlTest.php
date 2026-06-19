<?php

namespace Tests\Feature;

use App\Models\Krs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GraphqlTest extends TestCase
{
    use RefreshDatabase;

    private function headers(): array
    {
        return ['X-IAE-KEY' => config('iae.api_key')];
    }

    public function test_graphql_query_returns_selected_krs_fields(): void
    {
        Krs::query()->create([
            'nim' => '102022400045',
            'kode_mata_kuliah' => 'IAE401',
            'nama_mata_kuliah' => 'Integrasi Aplikasi Enterprise',
            'sks' => 3,
            'tahun_ajaran' => '2025/2026',
            'semester' => 'ganjil',
            'status_persetujuan' => 'pending',
        ]);

        $response = $this->withHeaders($this->headers())->postJson('/graphql', [
            'query' => 'query { krsList { id nim kode_mata_kuliah status_persetujuan } }',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.krsList.0.nim', '102022400045')
            ->assertJsonPath('data.krsList.0.kode_mata_kuliah', 'IAE401')
            ->assertJsonPath('data.krsList.0.status_persetujuan', 'pending');

        $this->assertArrayNotHasKey(
            'nama_mata_kuliah',
            $response->json('data.krsList.0')
        );
    }

    public function test_graphql_endpoint_requires_api_key(): void
    {
        $response = $this->postJson('/graphql', [
            'query' => 'query { krsList { id nim } }',
        ]);

        $response->assertUnauthorized()
            ->assertJsonPath('status', 'error');
    }
}
