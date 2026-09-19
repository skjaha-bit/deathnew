<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommercialBank\Models\Bank;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Landsbankinn', 'Íslandsbanki', 'Arion banki', 'Kvika banki'] as $name) {
            Bank::query()->updateOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
