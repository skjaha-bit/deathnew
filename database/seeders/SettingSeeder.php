<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'FYRIRHYGGJA', 'type' => 'string', 'group' => 'general'],
            ['key' => 'support_email', 'value' => 'support@fyrirhyggja.is', 'type' => 'string', 'group' => 'general'],
            ['key' => 'support_phone', 'value' => '+354 000 0000', 'type' => 'string', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
