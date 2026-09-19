<?php

namespace Modules\UserSubscriptions\Http\Services;

use Illuminate\Support\Collection;
use Modules\UserSubscriptions\Models\SubscriptionType;
use Modules\UserSubscriptions\Models\UserSubscription;

class UserSubscriptionService
{
    protected const FILLABLE_KEYS = [
        'subscription_type_id',
        'name',
        'associated_email',
        'billing_cycle',
        'amount',
        'power_of_attorney_termination',
    ];

    public function listForUser(int $userId)
    {
        return UserSubscription::query()
            ->with('subscriptionType')
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function create(int $userId, array $data): UserSubscription
    {
        $subscription = UserSubscription::query()->create([
            'user_id' => $userId,
            ...array_intersect_key($data, array_flip(self::FILLABLE_KEYS)),
        ]);

        return $subscription->fresh('subscriptionType');
    }

    public function update(UserSubscription $subscription, array $data): UserSubscription
    {
        $subscription->update(array_intersect_key($data, array_flip(self::FILLABLE_KEYS)));

        return $subscription->fresh('subscriptionType');
    }

    public function delete(UserSubscription $subscription): bool
    {
        return (bool) $subscription->delete();
    }

    /**
     * Active subscription types available for the "Type of subscription" dropdown.
     */
    public function typeOptions(): Collection
    {
        return SubscriptionType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }
}
