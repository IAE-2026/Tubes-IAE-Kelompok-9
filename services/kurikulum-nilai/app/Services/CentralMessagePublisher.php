<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CentralMessagePublisher
{
    public function __construct(
        private readonly IaeTokenService $tokenService
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function publish(string $event, array $data): void
    {
        $token = $this->tokenService->getValidToken();
        $url = config('services.iae.rabbitmq_publish_url');

        $payload = [
            'routing_key' => $event,
            'message' => [
                'event' => $event,
                'timestamp' => now()->toIso8601String(),
                'data' => $data,
            ],
        ];

        $response = Http::timeout(15)
            ->withToken($token)
            ->post($url, $payload);

        if (! $response->successful()) {
            Log::error('RabbitMQ publish failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'event' => $event,
            ]);

            throw new \RuntimeException('Publish RabbitMQ gagal: HTTP '.$response->status());
        }

        Log::info('RabbitMQ event published', [
            'event' => $event,
            'team_id' => config('services.iae.team_id'),
        ]);
    }
}
