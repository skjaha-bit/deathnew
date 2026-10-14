<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Services\ApiPayloadEncrypter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Encrypts the `data` part of every /api/* JSON response (see
 * ApiPayloadEncrypter). `success`, `message` and validation `errors` stay
 * readable, and `encrypted: true` tells the frontend to decrypt `data`.
 */
class EncryptApiResponse
{
    public function __construct(protected ApiPayloadEncrypter $encrypter) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! config('api_encryption.enabled') || ! $response instanceof JsonResponse) {
            return $response;
        }

        $payload = $response->getData();

        if (! is_object($payload) || ! property_exists($payload, 'data') || $payload->data === null) {
            return $response;
        }

        $payload->data = $this->encrypter->encrypt($payload->data);
        $payload->encrypted = true;

        return $response->setData($payload);
    }
}
