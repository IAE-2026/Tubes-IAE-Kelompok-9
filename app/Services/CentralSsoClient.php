<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CentralSsoClient
{
    protected string $baseUrl;

    protected string $apiKey;

    protected string $teamId;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('iae.sso.url', 'https://iae-sso.virtualfri.id'), '/');
        $this->apiKey = config('iae.api_key', 'KEY-MHS-109');
        $this->teamId = config('iae.sso.team_id', 'TEAM-09');
    }

    /**
     * Get M2M token with caching
     */
    public function getM2mToken(): ?string
    {
        return Cache::remember('iae_m2m_token', 3000, function () {
            try {
                $response = Http::timeout(5)
                    ->acceptJson()
                    ->post($this->baseUrl.'/api/v1/auth/token', [
                        'api_key' => $this->apiKey,
                    ]);

                if ($response->successful()) {
                    return $response->json('token');
                }

                Log::error('Failed to retrieve M2M token from Central SSO', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
            } catch (\Exception $e) {
                Log::error('Exception retrieving M2M token from Central SSO: '.$e->getMessage());
            }

            return null;
        });
    }

    /**
     * Send SOAP Audit log
     */
    public function sendAuditLog(string $activityName, array $logContent): ?string
    {
        $token = $this->getM2mToken();
        if (! $token) {
            Log::warning('SSO M2M token not available for audit log.');

            return null;
        }

        $jsonContent = json_encode($logContent);

        // Construct rigid XML SOAP Envelope
        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:iae="http://iae.central/audit">
  <soap:Body>
    <iae:AuditRequest>
      <iae:TeamID>'.htmlspecialchars($this->teamId).'</iae:TeamID>
      <iae:ActivityName>'.htmlspecialchars($activityName).'</iae:ActivityName>
      <iae:LogContent><![CDATA['.$jsonContent.']]></iae:LogContent>
    </iae:AuditRequest>
  </soap:Body>
</soap:Envelope>';

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'text/xml; charset=utf-8',
                ])
                ->withBody($xmlBody, 'text/xml')
                ->post($this->baseUrl.'/soap/v1/audit');

            if ($response->successful()) {
                $xmlString = $response->body();

                // Parse XML to extract ReceiptNumber
                if (preg_match('/<iae:ReceiptNumber>(.*?)<\/iae:ReceiptNumber>/', $xmlString, $matches)) {
                    return $matches[1];
                }

                Log::warning('SOAP regex match failed. Trying SimpleXML fallback.');

                // Fallback to SimpleXMLElement
                try {
                    $xml = new \SimpleXMLElement($xmlString);
                    $xml->registerXPathNamespace('iae', 'http://iae.central/audit');
                    $result = $xml->xpath('//iae:ReceiptNumber');
                    if (! empty($result)) {
                        return (string) $result[0];
                    }
                } catch (\Exception $e) {
                    Log::warning('SimpleXML failed to parse SOAP response: '.$e->getMessage());
                }
            }

            Log::error('Failed to send SOAP audit log to Central SSO', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('Exception sending SOAP audit log to Central SSO: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Publish message to RabbitMQ via HTTP Gateway
     */
    public function publishMessage(string $routingKey, array $message): bool
    {
        $token = $this->getM2mToken();
        if (! $token) {
            Log::warning('SSO M2M token not available for message publishing.');

            return false;
        }

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$token,
                ])
                ->post($this->baseUrl.'/api/v1/messages/publish', [
                    'routing_key' => $routingKey,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('Failed to publish message to RabbitMQ via Central SSO gateway', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
        } catch (\Exception $e) {
            Log::error('Exception publishing message via Central SSO gateway: '.$e->getMessage());
        }

        return false;
    }
}
