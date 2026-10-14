<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Prints a fresh API_ENCRYPTION_KEY. The same value goes in the backend
 * .env and in the frontend's env (e.g. VITE_API_ENCRYPTION_KEY) so it can
 * decrypt API responses. Rotating it requires redeploying both.
 */
class GenerateApiEncryptionKey extends Command
{
    protected $signature = 'api-encryption:key';

    protected $description = 'Generate a key for encrypting API responses (shared with the frontend).';

    public function handle(): int
    {
        $this->line('API_ENCRYPTION_KEY=base64:'.base64_encode(random_bytes(32)));

        return self::SUCCESS;
    }
}
