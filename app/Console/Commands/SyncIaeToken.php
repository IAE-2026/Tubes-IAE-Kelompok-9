<?php

namespace App\Console\Commands;

use App\Services\IaeTokenService;
use Illuminate\Console\Command;

class SyncIaeToken extends Command
{
    protected $signature = 'iae:sync-token';

    protected $description = 'Fetch IAE SSO token and persist it to .env automatically';

    public function handle(IaeTokenService $tokenService): int
    {
        $this->info('Fetching IAE SSO token...');

        try {
            $token = $tokenService->refreshToken(persistToEnv: true);
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $masked = substr($token, 0, 12).'...'.substr($token, -8);
        $expiresAt = env('IAE_SSO_TOKEN_EXPIRES_AT');

        $this->info('Token synced successfully.');
        $this->line("Preview: {$masked}");
        $this->line("Expires at: {$expiresAt}");

        return self::SUCCESS;
    }
}
