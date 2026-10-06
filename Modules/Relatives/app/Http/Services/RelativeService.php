<?php

namespace Modules\Relatives\Http\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Modules\Auth\Http\Services\NationalRegistryService;
use Modules\Relatives\Models\Relative;
use Modules\Relatives\Models\RelativePhone;

class RelativeService
{
    public function __construct(protected NationalRegistryService $registry) {}

    public function listForUser(int $userId)
    {
        return Relative::query()
            ->with(['emails', 'phones'])
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    /** The same person may be added by many users, but only once by each user. */
    public function alreadyAdded(int $userId, string $kennitala, ?int $exceptRelativeId = null): bool
    {
        return Relative::query()
            ->where('user_id', $userId)
            ->where('kennitala', preg_replace('/\D/', '', $kennitala))
            ->when($exceptRelativeId, fn ($q) => $q->whereKeyNot($exceptRelativeId))
            ->exists();
    }

    /**
     * Check a family member's kennitala in the national registry, same lookup as registration.
     * Only the name is passed on; address and contact details are not exposed.
     *
     * @return array{valid: bool, deceased: bool, kennitala?: string, name?: string}
     *
     * @throws \Modules\Auth\Exceptions\NationalRegistryUnavailableException
     */
    public function lookupKennitala(string $kennitala): array
    {
        $person = $this->registry->lookup($kennitala);

        if (! $person['valid']) {
            return ['valid' => false, 'deceased' => false];
        }

        return [
            'valid' => true,
            'deceased' => $person['deceased'],
            'kennitala' => $person['data']['kennitala'],
            'name' => $person['data']['name'],
        ];
    }

    public function create(int $userId, array $data): Relative
    {
        $emails = $data['emails'] ?? [];
        $phones = $data['phones'] ?? [];
        unset($data['emails'], $data['phones']);

        $relative = Relative::query()->create([
            ...$data,
            'user_id' => $userId,
        ]);

        $this->syncEmails($relative, $emails);
        $this->syncPhones($relative, $phones);

        return $relative->fresh(['emails', 'phones']);
    }

    public function update(Relative $relative, array $data): Relative
    {
        $emails = $data['emails'] ?? null;
        $phones = $data['phones'] ?? null;
        unset($data['emails'], $data['phones']);

        $relative->update($data);

        if ($emails !== null) {
            $this->syncEmails($relative, $emails);
        }

        if ($phones !== null) {
            $this->syncPhones($relative, $phones);
        }

        return $relative->fresh(['emails', 'phones']);
    }

    public function delete(Relative $relative): bool
    {
        return (bool) $relative->delete();
    }

    /**
     * Mark a relative as invited and log the intended invite dispatch.
     * Stand-in for real email/SMS delivery, which is not wired up yet.
     */
    public function invite(Relative $relative): Relative
    {
        $relative->update([
            'invite_status' => Relative::STATUS_INVITED,
            'invited_at' => Date::now(),
        ]);

        Log::channel('api')->info('Relative invite dispatched', [
            'relative_id' => $relative->id,
            'user_id' => $relative->user_id,
            'emails' => $relative->emails()->pluck('email')->all(),
            'phones' => $relative->phones()->get()->map(fn ($p) => $p->phone_code.$p->phone_number)->all(),
        ]);

        return $relative->fresh(['emails', 'phones']);
    }

    /**
     * Replace the relative's emails with exactly this set.
     */
    public function syncEmails(Relative $relative, array $emails): Collection
    {
        $emails = array_values(array_unique($emails));

        $relative->emails()->whereNotIn('email', $emails)->delete();

        $existing = $relative->emails()->pluck('email')->all();

        foreach (array_diff($emails, $existing) as $email) {
            $relative->emails()->create(['email' => $email]);
        }

        return $relative->emails()->get();
    }

    /**
     * Replace the relative's phone numbers with exactly this set.
     */
    public function syncPhones(Relative $relative, array $phones): Collection
    {
        $isSame = fn (array $a, $b) => $a['phone_code'] === $b->phone_code && $a['phone_number'] === $b->phone_number;

        foreach ($relative->phones as $existing) {
            $stillWanted = collect($phones)->contains(fn (array $p) => $isSame($p, $existing));

            if (! $stillWanted) {
                $existing->delete();
            }
        }

        $current = $relative->phones()->get();

        foreach ($phones as $phone) {
            $alreadyMine = $current->contains(fn (RelativePhone $existing) => $isSame($phone, $existing));

            if (! $alreadyMine) {
                $relative->phones()->create($phone);
            }
        }

        return $relative->phones()->get();
    }
}
