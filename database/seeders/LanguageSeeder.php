<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Language;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        Language::query()->updateOrCreate(['code' => 'en'], ['name' => 'English', 'is_active' => true, 'is_default' => true]);
        Language::query()->updateOrCreate(['code' => 'is'], ['name' => 'Íslenska', 'is_active' => true, 'is_default' => false]);
    }
}
