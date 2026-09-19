<?php

namespace Modules\Relatives\Http\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Date;
use Modules\Relatives\Models\Relative;

class RelativeService
{
    public function listForUser(int $userId)
    {
        return Relative::query()->where('user_id', $userId)->get();
    }

    public function create(int $userId, array $data): Relative
    {
        return Relative::query()->create([
            ...$data,
            'user_id' => $userId,
        ]);
    }

    public function update(Relative $relative, array $data): Relative
    {
        $relative->update($data);

        return $relative->fresh();
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
            'email' => $relative->email,
            'phone' => $relative->phone,
        ]);

        return $relative->fresh();
    }
}
