<?php

namespace Modules\Auth\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

/**
 * Proof that a person passed electronic ID sign-in, handed to the client by
 * /auth/eid/poll and required by /auth/register. It is encrypted with the app
 * key, so the client can neither read nor forge the kennitala inside it.
 */
class RegistrationToken
{
    private const TTL_MINUTES = 30;

    /** @param  array{kennitala: string, phone_code: ?string, phone_number: ?string}  $claims */
    public static function issue(array $claims): string
    {
        return Crypt::encryptString(json_encode($claims + ['expires_at' => now()->addMinutes(self::TTL_MINUTES)->timestamp]));
    }

    /** @return ?array{kennitala: string, phone_code: ?string, phone_number: ?string} null when invalid or expired. */
    public static function read(mixed $token): ?array
    {
        if (! is_string($token) || $token === '') {
            return null;
        }

        try {
            $claims = json_decode(Crypt::decryptString($token), true);
        } catch (DecryptException) {
            return null;
        }

        if (empty($claims['kennitala']) || ($claims['expires_at'] ?? 0) < now()->timestamp) {
            return null;
        }

        unset($claims['expires_at']);

        return $claims;
    }
}
