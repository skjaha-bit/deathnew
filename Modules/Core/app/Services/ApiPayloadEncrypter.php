<?php

namespace Modules\Core\Services;

use RuntimeException;
use stdClass;

/**
 * Encrypts API response values field-by-field for the frontend.
 *
 * Each value is JSON-encoded (so numbers/booleans keep their type after the
 * frontend JSON.parse()s it) and sealed with AES-256-GCM. Wire format of
 * one encrypted value: base64( iv[12 bytes] . ciphertext . authTag[16 bytes] )
 * - the layout WebCrypto's AES-GCM decrypt() expects.
 *
 * Ids and foreign keys (`id`, `*_id`, `*_ids`) and nulls are left as is.
 */
class ApiPayloadEncrypter
{
    private const CIPHER = 'aes-256-gcm';

    private const IV_LENGTH = 12;

    private const TAG_LENGTH = 16;

    private ?string $key = null;

    public function __construct(
        private readonly ?string $configuredKey,
        private readonly array $exceptKeys = [],
    ) {}

    public function encrypt(mixed $data, ?string $field = null): mixed
    {
        if ($field !== null && $this->isPlainField($field)) {
            return $data;
        }

        if ($data instanceof stdClass) {
            $out = new stdClass;

            foreach (get_object_vars($data) as $k => $v) {
                $out->{$k} = $this->encrypt($v, (string) $k);
            }

            return $out;
        }

        if (is_array($data)) {
            // List items inherit nothing from their numeric index.
            return array_map(fn ($v) => $this->encrypt($v), $data);
        }

        if ($data === null) {
            return null;
        }

        return $this->encryptValue($data);
    }

    public function encryptValue(mixed $value): string
    {
        $iv = random_bytes(self::IV_LENGTH);
        $plain = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR);

        $cipher = openssl_encrypt($plain, self::CIPHER, $this->key(), OPENSSL_RAW_DATA, $iv, $tag, '', self::TAG_LENGTH);

        if ($cipher === false) {
            throw new RuntimeException('API payload encryption failed.');
        }

        return base64_encode($iv.$cipher.$tag);
    }

    public function decryptValue(string $payload): mixed
    {
        $raw = base64_decode($payload, true);

        if ($raw === false || strlen($raw) < self::IV_LENGTH + self::TAG_LENGTH) {
            throw new RuntimeException('Invalid encrypted API value.');
        }

        $iv = substr($raw, 0, self::IV_LENGTH);
        $tag = substr($raw, -self::TAG_LENGTH);
        $cipher = substr($raw, self::IV_LENGTH, -self::TAG_LENGTH);

        $plain = openssl_decrypt($cipher, self::CIPHER, $this->key(), OPENSSL_RAW_DATA, $iv, $tag);

        if ($plain === false) {
            throw new RuntimeException('Invalid encrypted API value.');
        }

        return json_decode($plain, true, 512, JSON_THROW_ON_ERROR);
    }

    public function isPlainField(string $field): bool
    {
        return $field === 'id'
            || str_ends_with($field, '_id')
            || str_ends_with($field, '_ids')
            || in_array($field, $this->exceptKeys, true);
    }

    private function key(): string
    {
        if ($this->key !== null) {
            return $this->key;
        }

        $key = (string) $this->configuredKey;

        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7), true) ?: '';
        }

        if (strlen($key) !== 32) {
            throw new RuntimeException('API_ENCRYPTION_KEY must be a 32-byte key (run `php artisan api-encryption:key`).');
        }

        return $this->key = $key;
    }
}
