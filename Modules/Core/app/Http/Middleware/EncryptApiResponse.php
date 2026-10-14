<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Services\ApiPayloadEncrypter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Encrypts user-entered values in the `data` part of /api/* JSON responses
 * (see ApiPayloadEncrypter). Reference/system routes listed in
 * api_encryption.except_routes are sent plain. `success`, `message` and
 * validation `errors` stay readable, and `encrypted: true` marks a response
 * whose `data` may contain "enc:" values.
 */
class EncryptApiResponse
{
    public function __construct(protected ApiPayloadEncrypter $encrypter) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! config('api_encryption.enabled')
            || ! $response instanceof JsonResponse
            || $request->is(...config('api_encryption.except_routes', []))) {
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
