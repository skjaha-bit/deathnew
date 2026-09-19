<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Relatives\Models\Guardian;

class GuardianSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Anna Guðmundsdóttir',
            'Björn Þórsson',
            'Katrín Ólafsdóttir',
            'Sigurður Jónsson',
            'Guðrún Pétursdóttir',
            'Family Friend',
            'Neighbor',
        ] as $name) {
            Guardian::query()->updateOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
