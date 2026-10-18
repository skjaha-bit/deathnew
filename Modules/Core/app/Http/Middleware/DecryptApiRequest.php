<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Services\ApiPayloadEncrypter;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Counterpart of EncryptApiResponse: the frontend may send back "enc:" values
 * exactly as it received them, so every such string in the query/body is
 * decrypted here before validation. Strings that don't decrypt with the API
 * key are left as typed.
 */
class DecryptApiRequest
{
    public function __construct(protected ApiPayloadEncrypter $encrypter) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (config('api_encryption.enabled')) {
            $request->query->replace($this->decrypt($request->query->all()));
            $request->request->replace($this->decrypt($request->request->all()));

            if ($request->isJson()) {
                $request->json()->replace($this->decrypt($request->json()->all()));
            }
        }

        return $next($request);
    }

    private function decrypt(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($v) => $this->decrypt($v), $value);
        }

        if (! is_string($value) || ! str_starts_with($value, ApiPayloadEncrypter::PREFIX)) {
            return $value;
        }

        try {
            return $this->encrypter->decryptValue($value);
        } catch (RuntimeException|\JsonException) {
            return $value;
        }
    }
}
