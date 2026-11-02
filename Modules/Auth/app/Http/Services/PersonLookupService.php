<?php

namespace Modules\Auth\Http\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Auth\Exceptions\NationalRegistryUnavailableException;

/**
 * One-shot person lookup by kennitala through Taktikal, kept apart from NationalRegistryService
 * so login and relatives keep their own lookup. Uses the richest lookup type (NameAddressFamily)
 * and the PEP/sanctions watchlist, the only Taktikal source that flags a person as deceased.
 */
class PersonLookupService
{
    /**
     * @return array{valid: bool, person?: array<string, mixed>}
     *
     * @throws NationalRegistryUnavailableException when the registry can't be reached or answers unexpectedly.
     */
    public function lookup(string $kennitala): array
    {
        $ssn = preg_replace('/\D/', '', $kennitala);

        try {
            $response = $this->client()->get($this->url('/nationalRegistry'), [
                'lookupType' => 'NameAddressFamily',
                'flowKey' => config('services.taktikal.flow_key'),
                'ssn' => $ssn,
            ]);
        } catch (ConnectionException $e) {
            Log::error('Taktikal person lookup unreachable', ['message' => $e->getMessage()]);

            throw new NationalRegistryUnavailableException;
        }

        $body = $response->json() ?? [];

        if ($response->status() === 400 && $this->isInvalidSsn($body)) {
            return ['valid' => false];
        }

        if ($response->failed()) {
            Log::error('Taktikal person lookup failed', ['status' => $response->status(), 'body' => $response->body()]);

            throw new NationalRegistryUnavailableException;
        }

        if (empty($body['name'])) {
            return ['valid' => false];
        }

        $birthDate = $this->birthDateFromKennitala($ssn);
        ['deceased' => $deceased, 'deceased_at' => $deceasedAt] = $this->deceased($body['name'], $birthDate);
        [$street, $houseNumber] = $this->splitAddress($body['address'] ?? null);

        return [
            'valid' => true,
            'person' => [
                'kennitala' => $body['ssn'] ?? $ssn,
                'name' => $body['name'],
                // The registry holds no contact details; these fill in only if Taktikal starts sending them.
                'email' => $this->blankToNull($body['email'] ?? null),
                'phone' => $this->blankToNull($body['phoneNumber'] ?? null),
                'address' => $street,
                'house_number' => $houseNumber,
                'city' => $this->blankToNull($body['city'] ?? null),
                'postal_code' => $this->blankToNull($body['postalCode'] ?? null),
                'country' => 'IS',
                'gender' => $this->gender($body['genderCode'] ?? null),
                'birth_date' => $birthDate,
                'citizenship' => $this->blankToNull($body['citizenship'] ?? null),
                'family_status' => $this->blankToNull($body['familyStatus'] ?? null),
                'family_number' => $this->blankToNull($body['familyNumber'] ?? null),
                // Taktikal spells it "parnerSsn".
                'partner_kennitala' => $this->blankToNull($body['parnerSsn'] ?? $body['partnerSsn'] ?? null),
                'deceased' => $deceased,
                'deceased_at' => $deceasedAt,
            ],
        ];
    }

    /**
     * A same-name watchlist hit is not enough: when both sides know the birth date they must agree.
     * Never fails the lookup: if the watchlist is down the person is reported as alive.
     *
     * @return array{deceased: bool, deceased_at: ?string}
     */
    private function deceased(string $name, ?string $birthDate): array
    {
        $alive = ['deceased' => false, 'deceased_at' => null];

        try {
            $response = $this->client()->post($this->url('/watchlist/person/'), array_filter([
                'countryCode' => 'IS',
                'originalName' => $name,
                'dateOfBirth' => $birthDate,
                'matchType' => 'Exact',
                'flowKey' => config('services.taktikal.flow_key'),
            ]));
        } catch (ConnectionException $e) {
            Log::error('Taktikal watchlist unreachable', ['message' => $e->getMessage()]);

            return $alive;
        }

        if ($response->failed()) {
            Log::error('Taktikal watchlist lookup failed', ['status' => $response->status(), 'body' => $response->body()]);

            return $alive;
        }

        $match = collect($response->json('match') ?? [])->first(fn ($m) => ($m['deceased'] ?? false)
            && (! $birthDate || empty($m['dateOfBirth']) || str_starts_with($m['dateOfBirth'], $birthDate)));

        return $match ? ['deceased' => true, 'deceased_at' => $match['deceasedDate'] ?? null] : $alive;
    }

    private function client(): PendingRequest
    {
        return Http::withBasicAuth(config('services.taktikal.company_key'), config('services.taktikal.api_key'))
            ->acceptJson()
            ->timeout(15);
    }

    private function url(string $path): string
    {
        return rtrim(config('services.taktikal.url'), '/').$path;
    }

    private function isInvalidSsn(array $body): bool
    {
        $status = $body['responseStatus'] ?? [];

        return ($status['errorCode'] ?? null) === 'ssn.invalid'
            || collect($status['errors'] ?? [])->contains(fn ($e) => ($e['errorCode'] ?? null) === 'ssn.invalid');
    }

    /** Registers Iceland codes: 1 and 3 are male, 2 and 4 female, anything else (e.g. 7) other. */
    private function gender(?string $code): ?string
    {
        return match (trim((string) $code)) {
            '' => null,
            '1', '3' => 'male',
            '2', '4' => 'female',
            default => 'other',
        };
    }

    private function splitAddress(?string $address): array
    {
        $address = trim((string) $address);

        if ($address === '') {
            return [null, null];
        }

        if (preg_match('/^(\D+?)\s+(\d.*)$/u', $address, $m)) {
            return [trim($m[1]), trim($m[2], " \t,")];
        }

        return [$address, null];
    }

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

    private function blankToNull(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
