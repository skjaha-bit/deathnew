<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Subscription\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['code' => 'monthly', 'name' => 'Monthly subscription', 'price' => 1390, 'discount_percent' => 0, 'billing_interval' => 'monthly'],
            ['code' => 'yearly_1', 'name' => '1-year subscription', 'price' => 12000, 'discount_percent' => 28, 'billing_interval' => '1_year'],
            ['code' => 'yearly_5', 'name' => '5-year subscription', 'price' => 54000, 'discount_percent' => 10, 'billing_interval' => '5_year'],
            ['code' => 'yearly_10', 'name' => '10-year subscription', 'price' => 96000, 'discount_percent' => 20, 'billing_interval' => '10_year'],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::query()->updateOrCreate(
                ['code' => $plan['code']],
                $plan + ['currency' => 'ISK', 'is_active' => true]
            );
        }
    }
}
