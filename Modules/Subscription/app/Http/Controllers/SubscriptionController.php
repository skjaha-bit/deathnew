<?php

namespace Modules\Subscription\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponse;
use Modules\Subscription\Http\Requests\SelectSubscriptionPlanRequest;
use Modules\Subscription\Http\Services\SubscriptionService;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Models\SubscriptionPlan;

class SubscriptionController extends Controller
{
    use ApiResponse;

    public function __construct(protected SubscriptionService $subscriptionService) {}

    /**
     * Public listing of the available subscription plans (pricing info only).
     */
    public function plans()
    {
        $plans = $this->subscriptionService->activePlans();

        return $this->success($plans->map(fn (SubscriptionPlan $plan) => $this->formatSubscriptionPlan($plan))->values());
    }

    /**
     * The authenticated user's current subscription, if any.
     */
    public function current(Request $request)
    {
        $subscription = $this->subscriptionService->currentForUser($request->user()->id);

        return $this->success($subscription ? $this->formatSubscription($subscription) : null);
    }

    /**
     * Record the authenticated user's plan choice. No payment is processed here.
     */
    public function select(SelectSubscriptionPlanRequest $request)
    {
        try {
            $subscription = $this->subscriptionService->selectPlan(
                $request->user()->id,
                $request->validated('subscription_plan_id')
            );
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        }

        return $this->success($this->formatSubscription($subscription), 'Subscription plan selected.');
    }

    private function formatSubscription(Subscription $subscription): array
    {
        return [
            'id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'subscription_plan_id' => $subscription->subscription_plan_id,
            'status' => $subscription->status,
            'selected_at' => $subscription->selected_at?->toIso8601String(),
        ];
    }

    private function formatSubscriptionPlan(SubscriptionPlan $plan): array
    {
        return [
            'id' => $plan->id,
            'code' => $plan->code,
            'name' => $plan->name,
            'price' => $plan->price,
            'currency' => $plan->currency,
            'discount_percent' => $plan->discount_percent,
            'billing_interval' => $plan->billing_interval,
        ];
    }
}
