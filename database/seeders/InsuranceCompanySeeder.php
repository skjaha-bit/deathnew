<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Insurance\Models\InsuranceCompany;

class InsuranceCompanySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Sjóvá', 'VÍS', 'TM (Tryggingamiðstöðin)', 'Vörður tryggingar', 'Íslandstrygging'] as $name) {
            InsuranceCompany::query()->updateOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
