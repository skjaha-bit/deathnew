<?php

namespace Modules\CommercialBank\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommercialBank\Models\AccountType;
use Modules\CommercialBank\Models\BondType;
use Modules\CommercialBank\Models\FundType;
use Modules\CommercialBank\Models\LoanType;

class CommercialBankDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Checking', 'Savings', 'Currency Account', 'Youth Account'] as $name) {
            AccountType::query()->firstOrCreate(['name' => $name]);
        }

        foreach (['Mortgage', 'Auto Loan', 'Personal Loan', 'Overdraft'] as $name) {
            LoanType::query()->firstOrCreate(['name' => $name]);
        }

        foreach (['Government Bond', 'Corporate Bond', 'Municipal Bond', 'Housing Bond'] as $name) {
            BondType::query()->firstOrCreate(['name' => $name]);
        }

        foreach (['Mutual Fund', 'Index Fund', 'Money Market Fund', 'Pension Fund'] as $name) {
            FundType::query()->firstOrCreate(['name' => $name]);
        }
    }
}
