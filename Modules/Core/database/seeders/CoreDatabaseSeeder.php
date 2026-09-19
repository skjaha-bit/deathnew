<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Country;

class CoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'Iceland', 'short_code' => 'IS', 'country_code' => '+354'],
            ['name' => 'Denmark', 'short_code' => 'DK', 'country_code' => '+45'],
            ['name' => 'Norway', 'short_code' => 'NO', 'country_code' => '+47'],
            ['name' => 'Sweden', 'short_code' => 'SE', 'country_code' => '+46'],
            ['name' => 'Finland', 'short_code' => 'FI', 'country_code' => '+358'],
            ['name' => 'Faroe Islands', 'short_code' => 'FO', 'country_code' => '+298'],
            ['name' => 'United Kingdom', 'short_code' => 'GB', 'country_code' => '+44'],
            ['name' => 'Ireland', 'short_code' => 'IE', 'country_code' => '+353'],
            ['name' => 'Germany', 'short_code' => 'DE', 'country_code' => '+49'],
            ['name' => 'Netherlands', 'short_code' => 'NL', 'country_code' => '+31'],
            ['name' => 'Belgium', 'short_code' => 'BE', 'country_code' => '+32'],
            ['name' => 'France', 'short_code' => 'FR', 'country_code' => '+33'],
            ['name' => 'Spain', 'short_code' => 'ES', 'country_code' => '+34'],
            ['name' => 'Portugal', 'short_code' => 'PT', 'country_code' => '+351'],
            ['name' => 'Italy', 'short_code' => 'IT', 'country_code' => '+39'],
            ['name' => 'Poland', 'short_code' => 'PL', 'country_code' => '+48'],
            ['name' => 'Switzerland', 'short_code' => 'CH', 'country_code' => '+41'],
            ['name' => 'Austria', 'short_code' => 'AT', 'country_code' => '+43'],
            ['name' => 'United States', 'short_code' => 'US', 'country_code' => '+1'],
            ['name' => 'Canada', 'short_code' => 'CA', 'country_code' => '+1'],
            ['name' => 'Australia', 'short_code' => 'AU', 'country_code' => '+61'],
            ['name' => 'New Zealand', 'short_code' => 'NZ', 'country_code' => '+64'],
            ['name' => 'India', 'short_code' => 'IN', 'country_code' => '+91'],
        ];

        foreach ($countries as $country) {
            Country::query()->updateOrCreate(['short_code' => $country['short_code']], $country);
        }
    }
}
