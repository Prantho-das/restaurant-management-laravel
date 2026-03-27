<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Setting::create([
            'key' => 'fb_pixel_id',
            'value' => 'YOUR_PIXEL_ID',
            'group' => 'marketing',
        ]);
        \App\Models\Setting::create([
            'key' => 'fb_capi_token',
            'value' => 'YOUR_CAPI_TOKEN',
            'group' => 'marketing',
        ]);
        \App\Models\Setting::create([
            'key' => 'fb_test_event_code',
            'value' => 'TEST12345',
            'group' => 'marketing',
        ]);
    }
}
