<?php

namespace App\Http\Controllers;

class ReportHelper
{
    private static array $translations = [];
    private static string $locale = 'en';

    public static function setLocale(string $locale): void
    {
        self::$locale = $locale;
        self::loadTranslations();
    }

    public static function getLocale(): string
    {
        return self::$locale;
    }

    private static function loadTranslations(): void
    {
        $en = require base_path('lang/en/reports.php');
        $bn = require base_path('lang/bn/reports.php');

        self::$translations = [
            'en' => $en,
            'bn' => $bn,
        ];
    }

    public static function t(string $key): string
    {
        if (empty(self::$translations)) {
            self::loadTranslations();
        }

        return self::$translations[self::$locale][$key] ?? 
               (self::$translations['en'][$key] ?? $key);
    }

    /**
     * Translate order type to Bangla if locale is bn
     */
    public static function translateOrderType(string $type): string
    {
        $translations = [
            'dine_in' => ['en' => 'Dine In', 'bn' => 'ডাইন ইন'],
            'takeaway' => ['en' => 'Takeaway', 'bn' => 'টেকঅ্যাওয়ে'],
            'delivery' => ['en' => 'Delivery', 'bn' => 'ডেলিভারি'],
            'foodpanda' => ['en' => 'Foodpanda', 'bn' => 'ফুডপান্ডা'],
            'pathao' => ['en' => 'Pathao', 'bn' => 'পাথাও'],
        ];

        return $translations[$type][self::$locale] ?? $type;
    }

    /**
     * Translate payment method to Bangla if locale is bn
     */
    public static function translatePaymentMethod(string $method): string
    {
        $translations = [
            'cash' => ['en' => 'Cash', 'bn' => 'নগদ'],
            'card' => ['en' => 'Card', 'bn' => 'কার্ড'],
            'online' => ['en' => 'Online', 'bn' => 'অনলাইন'],
            'digital_wallet' => ['en' => 'Digital Wallet', 'bn' => 'ডিজিটাল ওয়ালেট'],
        ];

        return $translations[$method][self::$locale] ?? $method;
    }

    /**
     * Format date in the selected locale
     */
    public static function formatDate($date, string $format = 'M d, Y'): string
    {
        if (!$date instanceof \Carbon\Carbon) {
            $date = \Carbon\Carbon::parse($date);
        }

        if (self::$locale === 'bn') {
            // Convert English numbers to Bangla digits
            $english = $date->format($format);
            return self::toBanglaNumbers($english);
        }

        return $date->format($format);
    }

    /**
     * Convert English numbers to Bangla numbers
     */
    public static function toBanglaNumbers(string $input): string
    {
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($en, $bn, $input);
    }

    /**
     * Convert Bangla numbers to English numbers
     */
    public static function toEnglishNumbers(string $input): string
    {
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($bn, $en, $input);
    }
}