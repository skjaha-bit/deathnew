<?php

namespace Modules\UserSubscriptions\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponse;
use Modules\UserSubscriptions\Http\Requests\StoreUserSubscriptionRequest;
use Modules\UserSubscriptions\Http\Requests\UpdateUserSubscriptionRequest;
use Modules\UserSubscriptions\Http\Services\UserSubscriptionService;
use Modules\UserSubscriptions\Models\SubscriptionType;
use Modules\UserSubscriptions\Models\UserSubscription;

class UserSubscriptionController extends Controller
{
    use ApiResponse;

    public function __construct(protected UserSubscriptionService $userSubscriptionService) {}

    public function types()
    {
        $types = $this->userSubscriptionService->typeOptions();

        return $this->success($types->map(fn (SubscriptionType $type) => $this->formatType($type))->values()->all());
    }

    public function index(Request $request)
    {
        $subscriptions = $this->userSubscriptionService->listForUser($request->user()->id);

        return $this->success($subscriptions->map(fn (UserSubscription $subscription) => $this->formatSubscription($subscription))->values()->all());
    }

    public function store(StoreUserSubscriptionRequest $request)
    {
        $subscription = $this->userSubscriptionService->create($request->user()->id, $request->validated());

        return $this->success($this->formatSubscription($subscription), 'Subscription added.', 201);
    }

    public function show(Request $request, UserSubscription $userSubscription)
    {
        abort_if($userSubscription->user_id !== $request->user()->id, 403, 'You do not have access to this subscription.');

        $userSubscription->load('subscriptionType');

        return $this->success($this->formatSubscription($userSubscription));
    }

    public function update(UpdateUserSubscriptionRequest $request)
    {
        $data = $request->validated();

        $subscription = UserSubscription::query()->findOrFail($data['id']);

        abort_if($subscription->user_id !== $request->user()->id, 403, 'You do not have access to this subscription.');

        $subscription = $this->userSubscriptionService->update($subscription, $data);

        return $this->success($this->formatSubscription($subscription), 'Subscription updated.');
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => ['required', 'integer', 'exists:user_subscriptions,id']]);

        $subscription = UserSubscription::query()->findOrFail($request->input('id'));

        abort_if($subscription->user_id !== $request->user()->id, 403, 'You do not have access to this subscription.');

        $this->userSubscriptionService->delete($subscription);

        return $this->success(null, 'Subscription removed.');
    }

    private function formatType(SubscriptionType $type): array
    {
        return [
            'id' => $type->id,
            'name' => $type->name,
        ];
    }

    private function formatSubscription(UserSubscription $subscription): array
    {
        return [
            'id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'subscription_type_id' => $subscription->subscription_type_id,
            'subscription_type_name' => $subscription->subscriptionType?->name,
            'name' => $subscription->name,
            'associated_email' => $subscription->associated_email,
            'billing_cycle' => $subscription->billing_cycle,
            'amount' => $subscription->amount,
            'currency' => 'ISK',
            'power_of_attorney_termination' => $subscription->power_of_attorney_termination,
            'created_at' => $subscription->created_at?->toIso8601String(),
            'updated_at' => $subscription->updated_at?->toIso8601String(),
        ];
    }
}
