<?php

namespace Modules\Core\Services;

use RuntimeException;
use stdClass;

/**
 * Encrypts user-entered API response values field-by-field for the frontend.
 *
 * Each value is JSON-encoded (so numbers/booleans keep their type after the
 * frontend JSON.parse()s it) and sealed with AES-256-GCM. Wire format of
 * one encrypted value: "enc:" . base64( iv[12 bytes] . ciphertext . authTag[16 bytes] )
 * - the layout WebCrypto's AES-GCM decrypt() expects.
 *
 * Left as is: ids and foreign keys (`id`, `*_id`, `*_ids`), reference
 * labels beside their foreign key (`bank_name` next to `bank_id`), the
 * configured except/reference keys, and nulls.
 */
class ApiPayloadEncrypter
{
    public const PREFIX = 'enc:';

    private const CIPHER = 'aes-256-gcm';

    private const IV_LENGTH = 12;

    private const TAG_LENGTH = 16;

    private ?string $key = null;

    public function __construct(
        private readonly ?string $configuredKey,
        private readonly array $exceptKeys = [],
        private readonly array $referenceKeys = [],
    ) {}

    public function encrypt(mixed $data): mixed
    {
        if ($data instanceof stdClass) {
            $fields = get_object_vars($data);
            $out = new stdClass;

            foreach ($fields as $k => $v) {
                $k = (string) $k;
                $out->{$k} = $this->isPlainField($k, $fields) ? $v : $this->encrypt($v);
            }

            return $out;
        }

        if (is_array($data)) {
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

        return self::PREFIX.base64_encode($iv.$cipher.$tag);
    }

    public function decryptValue(string $payload): mixed
    {
        if (str_starts_with($payload, self::PREFIX)) {
            $payload = substr($payload, strlen(self::PREFIX));
        }

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

    /**
     * @param  array<string, mixed>  $siblings  The other fields of the same object.
     */
    public function isPlainField(string $field, array $siblings = []): bool
    {
        if ($field === 'id' || str_ends_with($field, '_id') || str_ends_with($field, '_ids')) {
            return true;
        }

        if (in_array($field, $this->exceptKeys, true) || in_array($field, $this->referenceKeys, true)) {
            return true;
        }

        // Reference label beside its foreign key, e.g. bank_name next to bank_id.
        return str_ends_with($field, '_name')
            && array_key_exists(substr($field, 0, -strlen('_name')).'_id', $siblings);
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
