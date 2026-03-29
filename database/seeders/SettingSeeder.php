<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Marketing settings
        Setting::firstOrCreate(['key' => 'fb_pixel_id'], [
            'value' => 'YOUR_PIXEL_ID',
            'group' => 'marketing',
            'type' => 'string',
        ]);
        Setting::firstOrCreate(['key' => 'fb_capi_token'], [
            'value' => 'YOUR_CAPI_TOKEN',
            'group' => 'marketing',
            'type' => 'string',
        ]);
        Setting::firstOrCreate(['key' => 'fb_test_event_code'], [
            'value' => 'TEST12345',
            'group' => 'marketing',
            'type' => 'string',
        ]);

        // Payment Gateway Settings - bKash
        Setting::firstOrCreate(['key' => 'payment_bkash_enabled'], [
            'value' => '0',
            'group' => 'general',
            'type' => 'boolean',
        ]);
        Setting::firstOrCreate(['key' => 'payment_bkash_sandbox'], [
            'value' => '1',
            'group' => 'general',
            'type' => 'boolean',
        ]);
        Setting::firstOrCreate(['key' => 'payment_bkash_store_username'], [
            'value' => '',
            'group' => 'general',
            'type' => 'string',
        ]);
        Setting::firstOrCreate(['key' => 'payment_bkash_store_password'], [
            'value' => '',
            'group' => 'general',
            'type' => 'string',
        ]);
        Setting::firstOrCreate(['key' => 'payment_bkash_app_key'], [
            'value' => '',
            'group' => 'general',
            'type' => 'string',
        ]);

        // Payment Gateway Settings - SSLCommerze
        Setting::firstOrCreate(['key' => 'payment_sslcommerze_enabled'], [
            'value' => '0',
            'group' => 'general',
            'type' => 'boolean',
        ]);
        Setting::firstOrCreate(['key' => 'payment_sslcommerze_store_id'], [
            'value' => '',
            'group' => 'general',
            'type' => 'string',
        ]);
        Setting::firstOrCreate(['key' => 'payment_sslcommerze_store_password'], [
            'value' => '',
            'group' => 'general',
            'type' => 'string',
        ]);
        Setting::firstOrCreate(['key' => 'payment_sslcommerze_sandbox'], [
            'value' => '1',
            'group' => 'general',
            'type' => 'boolean',
        ]);
    }
}
