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

    /** Birth date and nationality are opt-in on the Taktikal flow and may arrive top-level or in `meta`. */
    private const BIRTH_DATE_KEYS = ['birthDate', 'dateOfBirth', 'birthday'];

    private const NATIONALITY_KEYS = ['nationality', 'citizenship', 'countryOfCitizenship'];

    /** Þjóðskrá holds no contact details, so these only arrive if the Taktikal flow adds them. */
    private const EMAIL_KEYS = ['email', 'emailAddress'];

    private const PHONE_KEYS = ['phoneNumber', 'phone', 'mobile', 'mobileNumber', 'mobilePhone'];

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

        $meta = $body['meta'] ?? [];
        $birthDate = $this->firstValue($body, $meta, self::BIRTH_DATE_KEYS) ?? $this->birthDateFromKennitala($ssn);

        $deceasedAt = $this->firstValue($body, $meta, self::DEATH_DATE_KEYS);
        $deceased = $deceasedAt !== null
            || collect(self::DECEASED_FLAGS)->contains(fn ($key) => filter_var($body[$key] ?? $meta[$key] ?? false, FILTER_VALIDATE_BOOLEAN))
            || in_array(strtolower((string) ($body['status'] ?? '')), ['deceased', 'dead', 'látinn'], true);

        // The person lookup never flags death itself; Taktikal only exposes that through the PEP/sanctions watchlist.
        if (! $deceased && config('services.taktikal.watchlist_deceased_check')) {
            ['deceased' => $deceased, 'deceased_at' => $deceasedAt] = $this->watchlistDeceased($body['name'], $birthDate);
        }

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
                'birth_date' => $birthDate,
                'nationality' => $this->firstValue($body, $meta, self::NATIONALITY_KEYS),
                'email' => $this->firstValue($body, $meta, self::EMAIL_KEYS),
                'phone' => $this->firstValue($body, $meta, self::PHONE_KEYS),
                'deceased' => $deceased,
                'deceased_at' => $deceasedAt,
            ],
        ];
    }

    /**
     * PEP/sanctions lookup, the only Taktikal source that flags a person as deceased.
     * Never blocks a login: if the watchlist is down we treat the person as alive.
     *
     * @return array{deceased: bool, deceased_at: ?string}
     */
    private function watchlistDeceased(string $name, ?string $birthDate): array
    {
        $alive = ['deceased' => false, 'deceased_at' => null];

        try {
            $response = Http::withBasicAuth(config('services.taktikal.company_key'), config('services.taktikal.api_key'))
                ->acceptJson()
                ->timeout(15)
                ->post(rtrim(config('services.taktikal.url'), '/').'/watchlist/person/', array_filter([
                    'countryCode' => 'IS',
                    'originalName' => $name,
                    'dateOfBirth' => $birthDate,
                    'matchType' => 'Exact',
                    'flowKey' => config('services.taktikal.flow_key'),
                ]));
        } catch (ConnectionException $e) {
            $this->logError('Taktikal watchlist unreachable', ['message' => $e->getMessage()]);

            return $alive;
        }

        if ($response->failed()) {
            $this->logError('Taktikal watchlist lookup failed', ['status' => $response->status(), 'body' => $response->body()]);

            return $alive;
        }

        // A same-name match is not enough: when both sides know the birth date they must agree.
        $match = collect($response->json('match') ?? [])->first(fn ($m) => ($m['deceased'] ?? false)
            && (! $birthDate || empty($m['dateOfBirth']) || str_starts_with($m['dateOfBirth'], $birthDate)));

        return $match ? ['deceased' => true, 'deceased_at' => $match['deceasedDate'] ?? null] : $alive;
    }

    private function firstValue(array $body, array $meta, array $keys): ?string
    {
        return collect($keys)->map(fn ($key) => $body[$key] ?? $meta[$key] ?? null)->filter()->first();
    }

    /** A personal kennitala is DDMMYY-XXXC, where C is the century (9 = 1900s, 0 = 2000s, 8 = 1800s). */
    private function birthDateFromKennitala(string $ssn): ?string
    {
        $century = ['8' => '18', '9' => '19', '0' => '20'][$ssn[9] ?? ''] ?? null;

        if (strlen($ssn) !== 10 || ! $century) {
            return null;
        }

        [$day, $month, $year] = [substr($ssn, 0, 2), substr($ssn, 2, 2), $century.substr($ssn, 4, 2)];

        // Companies add 40 to the day, so they fail this check and get no birth date.
        return checkdate((int) $month, (int) $day, (int) $year) ? "$year-$month-$day" : null;
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
