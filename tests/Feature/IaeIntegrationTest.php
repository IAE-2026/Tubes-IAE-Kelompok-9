<?php

namespace Tests\Feature;

use App\Services\CentralMessagePublisher;
use App\Services\IaeTokenService;
use App\Services\JwksService;
use App\Services\SoapAuditClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IaeIntegrationTest extends TestCase
{
    public function test_token_service_refreshes_from_api(): void
    {
        Http::fake([
            '*/api/v1/auth/token' => Http::response([
                'status' => 'success',
                'token' => 'fake.jwt.token',
                'expires_in' => 3600,
                'token_type' => 'm2m',
            ], 200),
        ]);

        $token = app(IaeTokenService::class)->refreshToken(persistToEnv: false);

        $this->assertSame('fake.jwt.token', $token);
    }

    public function test_soap_audit_client_builds_and_parses_receipt(): void
    {
        Http::fake([
            '*/api/v1/auth/token' => Http::response([
                'token' => 'fake.jwt.token',
                'expires_in' => 3600,
            ], 200),
            '*/soap/v1/audit' => Http::response(
                '<soap:Envelope><soap:Body><iae:Status>SUCCESS</iae:Status><iae:ReceiptNumber>IAE-LOG-2026-TEST1234</iae:ReceiptNumber></soap:Body></soap:Envelope>',
                200
            ),
        ]);

        $client = app(SoapAuditClient::class);
        $receipt = $client->submit(['nim' => '102022580023'], 'NilaiRecorded');

        $this->assertSame('IAE-LOG-2026-TEST1234', $receipt);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/soap/v1/audit')
                && str_contains($request->body(), '<iae:TeamID>TIM-09</iae:TeamID>')
                && str_contains($request->body(), '<iae:ActivityName>NilaiRecorded</iae:ActivityName>');
        });
    }

    public function test_central_message_publisher_sends_nilai_recorded_event(): void
    {
        Http::fake([
            '*/api/v1/auth/token' => Http::response([
                'token' => 'fake.jwt.token',
                'expires_in' => 3600,
            ], 200),
            '*/api/v1/messages/publish' => Http::response(['status' => 'success'], 200),
        ]);

        $publisher = app(CentralMessagePublisher::class);
        $publisher->publish('nilai.recorded', [
            'nim' => '102022580023',
            'kode_matkul' => 'SI301',
        ]);

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return str_contains($request->url(), '/api/v1/messages/publish')
                && ($payload['message']['event'] ?? null) === 'nilai.recorded'
                && ($payload['message']['data']['nim'] ?? null) === '102022580023';
        });
    }

    public function test_jwks_service_fetches_public_keys(): void
    {
        Http::fake([
            '*/api/v1/auth/jwks' => Http::response([
                'keys' => [[
                    'kty' => 'RSA',
                    'use' => 'sig',
                    'alg' => 'RS256',
                    'kid' => 'iae-central-2026',
                    'n' => 'xF6NotMkNlUOHf0yg3APp2R9KeSDKkd_J2mAbkDIteaWLfphxsH6bffWR4ws4jPw3ScSBnycZttE_xfouOYgrTwYJWW9YN6poZusuur42jkvzgpX-BSOsouItfhJ8a4wQUibFCQarsFkKiYkeGuW_F6cr0O1oBgwFbbaR4bx1_RpIYvzkWQES-viZUnv7_u0EYMnwfFqMr0rDP78hDNzsqhlLPBiUNxKncQvW-q0ddXe4C8CpU5seH43jur8QFT6lE4LUeObTXHzXhX4qi-Mw4j16lR2ts5wPEynctcmd4eUobHxdHc_Nas5TNJC0VXO6BOISYj_ySvnP1mx5PSHNQ',
                    'e' => 'AQAB',
                ]],
            ], 200),
        ]);

        $keys = app(JwksService::class)->getKeySet();

        $this->assertNotEmpty($keys);
    }
}
