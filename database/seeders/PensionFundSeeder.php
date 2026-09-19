<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Pension\Models\PensionFund;

class PensionFundSeeder extends Seeder
{
    public function run(): void
    {
        $funds = [
            ['name' => 'Lífeyrissjóður verzlunarmanna', 'email' => 'lv@live.is', 'category' => 'both'],
            ['name' => 'Gildi lífeyrissjóður', 'email' => 'gildi@gildi.is', 'category' => 'both'],
            ['name' => 'Birta lífeyrissjóður', 'email' => 'birta@birta.is', 'category' => 'both'],
            ['name' => 'Lífeyrissjóður starfsmanna ríkisins (LSR)', 'email' => 'lsr@lsr.is', 'category' => 'obligatory'],
            ['name' => 'Almenni lífeyrissjóðurinn', 'email' => 'almenni@almenni.is', 'category' => 'both'],
            ['name' => 'Frjálsi lífeyrissjóðurinn', 'email' => 'frjalsi@frjalsi.is', 'category' => 'private'],
            ['name' => 'Stapi lífeyrissjóður', 'email' => 'stapi@stapi.is', 'category' => 'both'],
            ['name' => 'Brú lífeyrissjóður', 'email' => 'bru@lsbi.is', 'category' => 'obligatory'],
            ['name' => 'Íslenski lífeyrissjóðurinn', 'email' => 'is@isl.is', 'category' => 'private'],
            ['name' => 'Söfnunarsjóður lífeyrisréttinda', 'email' => 'ss@ss.is', 'category' => 'both'],
        ];

        foreach ($funds as $fund) {
            PensionFund::query()->updateOrCreate(['name' => $fund['name']], $fund + ['is_active' => true]);
        }
    }
}
