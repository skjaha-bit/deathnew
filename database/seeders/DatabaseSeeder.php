<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LanguageSeeder::class,
            SettingSeeder::class,
            RoleAndPermissionSeeder::class,
            AdminUserSeeder::class,
            PensionFundSeeder::class,
            InsuranceCompanySeeder::class,
            BankSeeder::class,
            LoanTypeSeeder::class,
            PropertyTypeSeeder::class,
            GuardianSeeder::class,
            TradeUnionSeeder::class,
            SubscriptionPlanSeeder::class,
            CmsContentSeeder::class,
        ]);
    }
}
