<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SoapAuditClient
{
    public function __construct(
        private readonly IaeTokenService $tokenService
    ) {}

    /**
     * @param  array<string, mixed>  $logContent
     */
    public function submit(array $logContent, string $activityName = 'NilaiRecorded'): string
    {
        $token = $this->tokenService->getValidToken();
        $teamId = config('services.iae.team_id');
        $url = config('services.iae.soap_audit_url');

        $jsonPayload = json_encode($logContent, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $xml = $this->buildEnvelope($teamId, $activityName, $jsonPayload);

        $response = Http::timeout(20)
            ->withToken($token)
            ->withHeaders(['Content-Type' => 'text/xml; charset=utf-8'])
            ->withBody($xml, 'text/xml')
            ->post($url);

        if (! $response->successful()) {
            Log::error('SOAP audit failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException('SOAP audit gagal: HTTP '.$response->status());
        }

        $receiptNumber = $this->extractReceiptNumber($response->body());

        if (! $receiptNumber) {
            throw new \RuntimeException('ReceiptNumber tidak ditemukan pada respons SOAP audit.');
        }

        return $receiptNumber;
    }

    private function buildEnvelope(string $teamId, string $activityName, string $jsonPayload): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:iae="http://iae.central/audit">
  <soap:Body>
    <iae:AuditRequest>
      <iae:TeamID>{$this->escapeXml($teamId)}</iae:TeamID>
      <iae:ActivityName>{$this->escapeXml($activityName)}</iae:ActivityName>
      <iae:LogContent><![CDATA[{$jsonPayload}]]></iae:LogContent>
    </iae:AuditRequest>
  </soap:Body>
</soap:Envelope>
XML;
    }

    private function extractReceiptNumber(string $body): ?string
    {
        if (preg_match('/<(?:iae:)?ReceiptNumber>([^<]+)<\/(?:iae:)?ReceiptNumber>/', $body, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    private function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
