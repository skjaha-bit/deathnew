<?php

namespace Modules\Subscription\Http\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Models\SubscriptionPlan;

class SubscriptionService
{
    /**
     * All plans currently available for selection, cheapest first.
     */
    public function activePlans(): Collection
    {
        return SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
    }

    /**
     * The subscription currently on record for the given user, if any.
     */
    public function currentForUser(int $userId): ?Subscription
    {
        return Subscription::where('user_id', $userId)->first();
    }

    /**
     * Record the user's plan choice. A user has at most one subscription row,
     * so selecting a new plan overwrites the existing one.
     */
    public function selectPlan(int $userId, int $planId): Subscription
    {
        $plan = SubscriptionPlan::where('is_active', true)->find($planId);

        if (! $plan) {
            throw new \RuntimeException('The selected plan is not available.');
        }

        return Subscription::updateOrCreate(
            ['user_id' => $userId],
            [
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'selected_at' => now(),
            ]
        );
    }
}
