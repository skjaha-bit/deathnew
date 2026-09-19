<?php

namespace Modules\Insurance\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Insurance\Models\InsuranceType;

class InsuranceDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            'Life insurance',
            'Home insurance',
            'Vehicle insurance',
            'Travel insurance',
            'Health insurance',
            'Accident insurance',
            'Child/family insurance',
            'Liquidity insurance',
            'Lausafjártrygging',
            'Another thing',
        ] as $name) {
            InsuranceType::query()->firstOrCreate(['name' => $name]);
        }
    }
}
