<?php

namespace App\Support;

/**
 * Keyed hashes ("blind indexes") for encrypted identity columns.
 *
 * An `encrypted` column is randomly salted, so WHERE / UNIQUE can't match
 * it. Next to each one we store HMAC-SHA256(normalized value) in a *_hash
 * column and query that instead. The HMAC key is derived from APP_KEY, so
 * the hashes can't be recomputed without it (rotating APP_KEY means
 * re-encrypting and re-hashing anyway).
 */
class BlindIndex
{
    public static function email(?string $email): ?string
    {
        $email = mb_strtolower(trim((string) $email));

        return $email === '' ? null : self::hash('email', $email);
    }

    /** Digits only, so "010101-1234" and "0101011234" match. */
    public static function kennitala(?string $kennitala): ?string
    {
        $kennitala = preg_replace('/\D/', '', (string) $kennitala);

        return $kennitala === '' ? null : self::hash('kennitala', $kennitala);
    }

    /** Digits only on both parts, so "+354" and "354" match. */
    public static function phone(?string $code, ?string $number): ?string
    {
        $number = preg_replace('/\D/', '', (string) $number);

        if ($number === '') {
            return null;
        }

        $code = ltrim(preg_replace('/\D/', '', (string) $code), '0');

        return self::hash('phone', $code.'|'.$number);
    }

    private static function hash(string $type, string $value): string
    {
        return hash_hmac('sha256', $type.':'.$value, self::key());
    }

    private static function key(): string
    {
        static $key = null;

        if ($key === null) {
            $appKey = (string) config('app.key');

            if (str_starts_with($appKey, 'base64:')) {
                $appKey = base64_decode(substr($appKey, 7));
            }

            $key = hash_hmac('sha256', 'blind-index', $appKey, true);
        }

        return $key;
    }
}
