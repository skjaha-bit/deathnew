<?php

namespace Modules\AfterMyDay\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AfterMyDay\Models\ArrangementType;
use Modules\AfterMyDay\Models\CeremonyType;
use Modules\AfterMyDay\Models\FuneralServiceType;

class AfterMyDayDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Church ceremony', 'Civil ceremony', 'No ceremony'] as $name) {
            CeremonyType::query()->firstOrCreate(['name' => $name]);
        }

        foreach (['Burial', 'Cremation'] as $name) {
            ArrangementType::query()->firstOrCreate(['name' => $name]);
        }

        foreach (['Full service', 'Memorial service only', 'Direct cremation'] as $name) {
            FuneralServiceType::query()->firstOrCreate(['name' => $name]);
        }
    }
}
