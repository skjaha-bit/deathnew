<?php

namespace Modules\Auth\Http\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Auth\Exceptions\ElectronicIdException;
use Throwable;

/**
 * Icelandic electronic ID (Auðkenni) sign-in through Taktikal: start sends a
 * prompt to the person's phone, poll returns who they are once they approve.
 */
class ElectronicIdService
{
    /**
     * @param  string  $context  'Sim' (e-ID on the SIM card) or 'App' (Auðkenni app).
     * @return array{auth_request_id: string, verification_code: ?string, polling_interval: int}
     *
     * @throws ElectronicIdException
     */
    public function start(?string $kennitala, ?string $phoneNumber, string $context = 'Sim'): array
    {
        $response = $this->send('/auth/start', array_filter([
            'ssn' => $kennitala ? preg_replace('/\D/', '', $kennitala) : null,
            'phoneNumber' => $phoneNumber,
            'flowKey' => config('services.taktikal.flow_key'),
            'authenticationContextType' => $context,
            'includeVerificationCode' => true,
        ]));

        if (empty($response['authRequestId'])) {
            $this->logError('Taktikal e-ID start returned no authRequestId', ['body' => $response]);

            throw ElectronicIdException::unavailable();
        }

        return [
            'auth_request_id' => $response['authRequestId'],
            'verification_code' => $response['verificationCode'] ?? null,
            'polling_interval' => (int) ($response['pollingInterval'] ?? 0),
        ];
    }

    /**
     * @return ?array{name: string, kennitala: string, phone_number: ?string, email: ?string, address: ?string, postal_code: ?string, city: ?string}
     *                null while the person has not approved the prompt yet.
     *
     * @throws ElectronicIdException
     */
    public function poll(string $authRequestId): ?array
    {
        $response = $this->send('/auth/poll', [
            'authRequestId' => $authRequestId,
            'flowKey' => config('services.taktikal.flow_key'),
            'lookupType' => 'NameAddress',
        ]);

        $customer = $response['customer'] ?? null;

        if (empty($customer['ssn'])) {
            return null;
        }

        return [
            'name' => $customer['name'] ?? null,
            'kennitala' => $customer['ssn'],
            'phone_number' => $customer['phoneNumber'] ?? null,
            'email' => $customer['email'] ?? null,
            'address' => $customer['address'] ?? null,
            'postal_code' => $customer['postalCode'] ?? null,
            'city' => $customer['city'] ?? null,
        ];
    }

    /** @throws ElectronicIdException */
    private function send(string $path, array $payload): array
    {
        try {
            $response = $this->client()->post(rtrim(config('services.taktikal.url'), '/').$path, $payload);
        } catch (ConnectionException $e) {
            $this->logError('Taktikal e-ID unreachable', ['path' => $path, 'message' => $e->getMessage()]);

            throw ElectronicIdException::unavailable();
        }

        if ($response->clientError()) {
            $this->logFailure($path, $response);

            throw ElectronicIdException::rejected();
        }

        if ($response->failed()) {
            $this->logFailure($path, $response);

            throw ElectronicIdException::unavailable();
        }

        return $response->json() ?? [];
    }

    private function client(): PendingRequest
    {
        return Http::withBasicAuth(config('services.taktikal.company_key'), config('services.taktikal.api_key'))
            ->acceptJson()
            ->timeout(15);
    }

    private function logFailure(string $path, Response $response): void
    {
        $this->logError('Taktikal e-ID request failed', [
            'path' => $path,
            'status' => $response->status(),
            'error' => $response->json('responseStatus.errorCode'),
        ]);
    }

    /** A broken log file must never block a login. */
    private function logError(string $message, array $context): void
    {
        try {
            Log::channel('api')->error($message, $context);
        } catch (Throwable) {
        }
    }
}
