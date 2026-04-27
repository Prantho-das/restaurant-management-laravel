<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS using Metrotel or generic API
     */
    public function send(string $phone, string $message): bool
    {
        $phone = $this->formatNumber($phone);

        $provider = Setting::getValue('sms_provider', 'metrotel');
        $apiKey = Setting::getValue('sms_api_key');
        $senderId = Setting::getValue('sms_sender_id');

        if (! $apiKey) {
            Log::warning("SMS API Key not set. SMS to $phone not sent.");

            return false;
        }

        try {
            if ($provider === 'metrotel') {
                return $this->sendMetrotel($phone, $message, $apiKey, $senderId);
            }

            // Fallback or generic provider
            return $this->sendGeneric($phone, $message, $apiKey, $senderId);
        } catch (\Exception $e) {
            Log::error('SMS Sending Failed: '.$e->getMessage());

            return false;
        }
    }

    private function sendMetrotel(string $phone, string $message, string $apiKey, ?string $senderId): bool
    {
        $response = Http::get('https://api.metrotel.com.bd/smsapi', [
            'api_key' => $apiKey,
            'type' => 'text',
            'contacts' => $phone,
            'senderid' => $senderId,
            'msg' => $message,
        ]);

        return $response->successful();
    }

    private function sendGeneric(string $phone, string $message, string $apiKey, ?string $senderId): bool
    {
        // Example for another common BD provider like BulkSMS BD
        $response = Http::get('https://bulksmsbd.net/api/smsapi', [
            'api_key' => $apiKey,
            'type' => 'text',
            'number' => $phone,
            'senderid' => $senderId,
            'message' => $message,
        ]);

        return $response->successful();
    }

    private function formatNumber(string $phone): string
    {
        // Remove non-numeric
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Ensure 88 prefix for BD
        if (strlen($phone) === 11 && str_starts_with($phone, '01')) {
            $phone = '88'.$phone;
        }

        return $phone;
    }
}
