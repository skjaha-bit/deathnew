<?php

namespace Modules\Core\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GatewayApiSmsDriver implements SmsGatewayInterface
{
    public function send(string $phoneCode, string $phoneNumber, string $message): bool
    {
        $msisdn = preg_replace('/\D/', '', $phoneCode.$phoneNumber);

        try {
            $response = Http::withBasicAuth(config('services.gatewayapi.token'), '')
                ->post(config('services.gatewayapi.url'), [
                    'sender' => config('services.gatewayapi.sender'),
                    'message' => $message,
                    'recipients' => [
                        ['msisdn' => (int) $msisdn],
                    ],
                ]);

            if ($response->failed()) {
                Log::channel('api')->error('GatewayAPI SMS send failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (Throwable $e) {
            Log::channel('api')->error('GatewayAPI SMS send exception', ['message' => $e->getMessage()]);

            return false;
        }
    }
}
