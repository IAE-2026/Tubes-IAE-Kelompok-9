<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndDocumentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_rest_endpoint_requires_api_key(): void
    {
        $response = $this->getJson('/api/v1/krs');

        $response->assertUnauthorized()
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'API key tidak valid atau tidak dikirim.');
    }

    public function test_rest_endpoint_rejects_wrong_api_key(): void
    {
        $response = $this->withHeaders(['X-IAE-KEY' => 'wrong-key'])->getJson('/api/v1/krs');

        $response->assertUnauthorized()
            ->assertJsonPath('status', 'error');
    }

    public function test_swagger_ui_is_available(): void
    {
        $response = $this->get('/api/documentation');

        $response->assertOk()
            ->assertSee('Swagger UI KRS-Service');
    }

    public function test_openapi_json_is_available(): void
    {
        $response = $this->getJson('/docs/openapi.json');

        $response->assertOk()
            ->assertJsonPath('info.title', 'KRS-Service API')
            ->assertJsonPath('components.securitySchemes.IaeApiKey.name', 'X-IAE-KEY');
    }

    public function test_graphiql_page_is_available(): void
    {
        $response = $this->get('/graphiql');

        $response->assertOk()
            ->assertSee('GraphiQL KRS-Service');
    }
}
