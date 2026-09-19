<?php

namespace Modules\Overview\Http\Services;

use Modules\AfterMyDay\Models\DeathNotice;
use Modules\AfterMyDay\Models\Funeral;
use Modules\CommercialBank\Models\UserCommercialBank;
use Modules\Insurance\Models\InsurancePolicy;
use Modules\Pension\Models\PensionEntry;
use Modules\Relatives\Models\Relative;
use Modules\Subscription\Models\Subscription;
use Modules\TradeUnion\Models\UserTradeUnion;

class OverviewService
{
    /**
     * A single "profile completion" dashboard: which sections the user has
     * started, and an overall percentage across the app's modules.
     */
    public function getDashboard(int $userId): array
    {
        $modules = [
            'relatives' => Relative::query()->where('user_id', $userId)->exists(),
            'trade_union' => UserTradeUnion::query()->where('user_id', $userId)->exists(),
            'commercial_bank' => UserCommercialBank::query()->where('user_id', $userId)->exists(),
            'pension' => PensionEntry::query()->where('user_id', $userId)->exists(),
            'insurance' => InsurancePolicy::query()->where('user_id', $userId)->exists(),
            'funeral' => Funeral::query()->where('user_id', $userId)->exists(),
            'death_notice' => DeathNotice::query()->where('user_id', $userId)->exists(),
        ];

        $completedCount = count(array_filter($modules));
        $totalCount = count($modules);

        $subscription = Subscription::query()->where('user_id', $userId)->first();

        return [
            'modules' => $modules,
            'completion_percent' => $totalCount > 0 ? (int) round(($completedCount / $totalCount) * 100) : 0,
            'completed_count' => $completedCount,
            'total_count' => $totalCount,
            'subscription' => $subscription ? [
                'subscription_plan_id' => $subscription->subscription_plan_id,
                'status' => $subscription->status,
            ] : null,
        ];
    }
}
