<?php

namespace Modules\Subscription\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Subscription\Models\SubscriptionPlan;

class SubscriptionPlanController extends Controller
{
    public const BILLING_INTERVALS = ['monthly', 'quarterly', 'yearly'];

    public function index(Request $request)
    {
        $items = SubscriptionPlan::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->query('search').'%'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('subscription::admin.subscription-plans.index', [
            'items' => $items,
            'billingIntervals' => self::BILLING_INTERVALS,
        ]);
    }

    protected function rules(?int $ignoreId = null): array
    {
        return [
            'code' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9_\-]+$/', 'unique:subscription_plans,code'.($ignoreId ? ','.$ignoreId : '')],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'billing_interval' => ['required', 'string', 'in:'.implode(',', self::BILLING_INTERVALS)],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());
        $data['is_active'] = $request->boolean('is_active', true);
        $data['discount_percent'] = $data['discount_percent'] ?? 0;

        $item = SubscriptionPlan::query()->create($data);

        return response()->json(['res' => true, 'msg' => 'Created successfully.', 'item' => $item]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $item = SubscriptionPlan::query()->findOrFail($id);
        $data = $request->validate($this->rules($item->id));
        $data['is_active'] = $request->boolean('is_active', $item->is_active);
        $data['discount_percent'] = $data['discount_percent'] ?? 0;

        $item->update($data);

        return response()->json(['res' => true, 'msg' => 'Updated successfully.', 'item' => $item]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $item = SubscriptionPlan::query()->findOrFail($id);
        $item->update(['is_active' => ! $item->is_active]);

        return response()->json(['res' => true, 'msg' => $item->is_active ? 'Activated.' : 'Deactivated.', 'is_active' => $item->is_active]);
    }

    public function destroy(int $id): JsonResponse
    {
        SubscriptionPlan::query()->findOrFail($id)->delete();

        return response()->json(['res' => true, 'msg' => 'Deleted successfully.']);
    }
}
