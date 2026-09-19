<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\TradeUnion\Models\TradeUnion;

class TradeUnionSeeder extends Seeder
{
    public function run(): void
    {
        $unions = [
            'VR (Verzlunarmannafélag Reykjavíkur)',
            'Efling stéttarfélag',
            'BHM (Bandalag háskólamanna)',
            'BSRB',
            'Rafiðnaðarsamband Íslands',
            'Kennarasamband Íslands',
        ];

        foreach ($unions as $name) {
            TradeUnion::query()->updateOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
