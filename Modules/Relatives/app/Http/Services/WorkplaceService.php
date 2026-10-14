<?php

namespace Modules\Relatives\Http\Services;

use Illuminate\Support\Collection;
use Modules\Relatives\Models\Workplace;
use Modules\Relatives\Models\WorkplacePhone;

class WorkplaceService
{
    public function listForUser(int $userId)
    {
        return Workplace::query()
            ->with(['emails', 'phones'])
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function create(int $userId, array $data): Workplace
    {
        $emails = $data['emails'] ?? [];
        $phones = $data['phones'] ?? [];
        unset($data['emails'], $data['phones']);

        $workplace = Workplace::query()->create([
            ...$data,
            'user_id' => $userId,
        ]);

        $this->syncEmails($workplace, $emails);
        $this->syncPhones($workplace, $phones);

        return $workplace->fresh(['emails', 'phones']);
    }

    public function update(Workplace $workplace, array $data): Workplace
    {
        $emails = $data['emails'] ?? null;
        $phones = $data['phones'] ?? null;
        unset($data['emails'], $data['phones']);

        $workplace->update($data);

        if ($emails !== null) {
            $this->syncEmails($workplace, $emails);
        }

        if ($phones !== null) {
            $this->syncPhones($workplace, $phones);
        }

        return $workplace->fresh(['emails', 'phones']);
    }

    public function delete(Workplace $workplace): bool
    {
        return (bool) $workplace->delete();
    }

    /**
     * Replace the workplace's emails with exactly this set.
     */
    public function syncEmails(Workplace $workplace, array $emails): Collection
    {
        $emails = array_values(array_unique($emails));

        // Emails are encrypted at rest, so they are compared in PHP rather than with whereNotIn().
        foreach ($workplace->emails()->get() as $existingEmail) {
            if (! in_array($existingEmail->email, $emails, true)) {
                $existingEmail->delete();
            }
        }

        $existing = $workplace->emails()->get()->pluck('email')->all();

        foreach (array_diff($emails, $existing) as $email) {
            $workplace->emails()->create(['email' => $email]);
        }

        return $workplace->emails()->get();
    }

    /**
     * Replace the workplace's phone numbers with exactly this set.
     */
    public function syncPhones(Workplace $workplace, array $phones): Collection
    {
        $isSame = fn (array $a, $b) => $a['phone_code'] === $b->phone_code && $a['phone_number'] === $b->phone_number;

        foreach ($workplace->phones as $existing) {
            $stillWanted = collect($phones)->contains(fn (array $p) => $isSame($p, $existing));

            if (! $stillWanted) {
                $existing->delete();
            }
        }

        $current = $workplace->phones()->get();

        foreach ($phones as $phone) {
            $alreadyMine = $current->contains(fn (WorkplacePhone $existing) => $isSame($phone, $existing));

            if (! $alreadyMine) {
                $workplace->phones()->create($phone);
            }
        }

        return $workplace->phones()->get();
    }
}
