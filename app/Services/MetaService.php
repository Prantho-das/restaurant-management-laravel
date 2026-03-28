<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaService
{
    protected ?string $pixelId;

    protected ?string $accessToken;

    protected ?string $testEventCode;

    public function __construct()
    {
        $this->pixelId = Setting::where('key', 'fb_pixel_id')->value('value');
        $this->accessToken = Setting::where('key', 'fb_capi_token')->value('value');
        $this->testEventCode = Setting::where('key', 'fb_test_event_code')->value('value');
    }

    public function sendEvent(string $eventName, array $data = [], array $userData = [])
    {
        if (! $this->pixelId || ! $this->accessToken) {
            return;
        }

        $payload = [
            'data' => [
                [
                    'event_name' => $eventName,
                    'event_time' => time(),
                    'action_source' => 'website',
                    'event_source_url' => request()->fullUrl(),
                    'user_data' => array_merge([
                        'client_ip_address' => request()->ip(),
                        'client_user_agent' => request()->userAgent(),
                    ], $userData),
                    'custom_data' => $data,
                ],
            ],
        ];

        if ($this->testEventCode) {
            $payload['test_event_code'] = $this->testEventCode;
        }

        try {
            $response = Http::post("https://graph.facebook.com/v19.0/{$this->pixelId}/events?access_token={$this->accessToken}", $payload);

            if ($response->failed()) {
                Log::error('Meta CAPI Error: '.$response->body());
            }
        } catch (\Exception $e) {
            Log::error('Meta CAPI Exception: '.$e->getMessage());
        }
    }
}
