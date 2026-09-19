<?php

namespace Modules\UserSubscriptions\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\UserSubscriptions\Models\SubscriptionType;

class UserSubscriptionsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Streaming', 'Media / Press', 'Gym', 'Club', 'Other'] as $name) {
            SubscriptionType::query()->firstOrCreate(['name' => $name]);
        }
    }
}
