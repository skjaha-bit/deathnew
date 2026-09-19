<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Property\Models\PropertyType;

class PropertyTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['House', 'Apartment', 'Summer House', 'Land', 'Commercial Property', 'Garage/Storage'] as $name) {
            PropertyType::query()->updateOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
