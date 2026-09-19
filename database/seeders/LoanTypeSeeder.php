<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommercialBank\Models\LoanType;

class LoanTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Mortgage', 'Auto Loan', 'Personal Loan', 'Overdraft'] as $name) {
            LoanType::query()->updateOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
