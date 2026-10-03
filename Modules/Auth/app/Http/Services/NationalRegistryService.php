<?php

namespace Modules\Auth\Http\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Auth\Exceptions\NationalRegistryUnavailableException;
use Throwable;

/**
 * Looks a kennitala up in the Icelandic national registry (Þjóðskrá) through Taktikal.
 */
class NationalRegistryService
{
    /**
     * Keys Taktikal / Þjóðskrá may use to flag a deceased person. The documented
     * response only has name/ssn/address/postalCode/city, so we check them all.
     */
    private const DECEASED_FLAGS = ['deceased', 'isDeceased', 'isDead', 'dead'];

    private const DEATH_DATE_KEYS = ['deceasedDate', 'dateOfDeath', 'deathDate', 'diedAt'];

    /**
     * @return array{valid: bool, deceased: bool, deceased_at: ?string, data: array}
     *
     * @throws NationalRegistryUnavailableException when the registry can't be reached or answers unexpectedly.
     */
    public function lookup(string $kennitala): array
    {
        $ssn = preg_replace('/\D/', '', $kennitala);

        try {
            $response = Http::withBasicAuth(config('services.taktikal.company_key'), config('services.taktikal.api_key'))
                ->acceptJson()
                ->timeout(15)
                ->get(rtrim(config('services.taktikal.url'), '/').'/nationalRegistry', [
                    'lookupType' => 'NameAddress',
                    'flowKey' => config('services.taktikal.flow_key'),
                    'ssn' => $ssn,
                ]);
        } catch (ConnectionException $e) {
            $this->logError('Taktikal national registry unreachable', ['message' => $e->getMessage()]);

            throw new NationalRegistryUnavailableException;
        }

        $body = $response->json() ?? [];

        if ($response->status() === 400 && $this->hasError($body, 'ssn.invalid')) {
            return ['valid' => false, 'deceased' => false, 'deceased_at' => null, 'data' => []];
        }

        if ($response->failed()) {
            $this->logError('Taktikal national registry lookup failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new NationalRegistryUnavailableException;
        }

        if (empty($body['name'])) {
            return ['valid' => false, 'deceased' => false, 'deceased_at' => null, 'data' => []];
        }

        $deceasedAt = collect(self::DEATH_DATE_KEYS)->map(fn ($key) => $body[$key] ?? null)->filter()->first();
        $deceased = $deceasedAt !== null
            || collect(self::DECEASED_FLAGS)->contains(fn ($key) => filter_var($body[$key] ?? false, FILTER_VALIDATE_BOOLEAN))
            || in_array(strtolower((string) ($body['status'] ?? '')), ['deceased', 'dead', 'látinn'], true);

        return [
            'valid' => true,
            'deceased' => $deceased,
            'deceased_at' => $deceasedAt,
            'data' => [
                'name' => $body['name'],
                'kennitala' => $body['ssn'] ?? $ssn,
                'address' => $body['address'] ?? null,
                'postal_code' => $body['postalCode'] ?? null,
                'city' => $body['city'] ?? null,
            ],
        ];
    }

    /** A broken log file must never block a login. */
    private function logError(string $message, array $context): void
    {
        try {
            Log::channel('api')->error($message, $context);
        } catch (Throwable) {
        }
    }

    private function hasError(array $body, string $code): bool
    {
        $status = $body['responseStatus'] ?? [];

        return ($status['errorCode'] ?? null) === $code
            || collect($status['errors'] ?? [])->contains(fn ($e) => ($e['errorCode'] ?? null) === $code && ($e['fieldName'] ?? null) === 'Ssn');
    }
}
