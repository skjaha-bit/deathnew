<?php

namespace Modules\TradeUnion\Http\Services;

use Illuminate\Support\Collection;
use Modules\TradeUnion\Models\TradeUnion;
use Modules\TradeUnion\Models\UserTradeUnion;

class TradeUnionService
{
    public function listForUser(int $userId)
    {
        return UserTradeUnion::query()->where('user_id', $userId)->get();
    }

    public function create(int $userId, array $data): UserTradeUnion
    {
        return UserTradeUnion::query()->create([
            ...$data,
            'user_id' => $userId,
        ]);
    }

    public function update(UserTradeUnion $userTradeUnion, array $data): UserTradeUnion
    {
        $userTradeUnion->update($data);

        return $userTradeUnion->fresh();
    }

    public function delete(UserTradeUnion $userTradeUnion): bool
    {
        return (bool) $userTradeUnion->delete();
    }

    /**
     * Active trade unions available for the "Trade union" dropdown.
     */
    public function activeOptions(): Collection
    {
        return TradeUnion::query()->where('is_active', true)->get(['id', 'name']);
    }
}
