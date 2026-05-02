<?php

use App\Http\Controllers\ReportHelper;

if (!function_exists('t')) {
    function t(string $key): string {
        return ReportHelper::t($key);
    }
}

if (!function_exists('formatNum')) {
    function formatNum($num, int $decimals = 2): string {
        $locale = $GLOBALS['locale'] ?? 'en';
        $num = number_format((float) $num, $decimals);
        if ($locale === 'bn') {
            return ReportHelper::toBanglaNumbers($num);
        }
        return $num;
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date, string $format = 'M d, Y'): string {
        return ReportHelper::formatDate($date, $format);
    }
}

if (!function_exists('tt')) {
    function tt(string $type): string {
        return ReportHelper::translateOrderType($type);
    }
}

if (!function_exists('tm')) {
    function tm(string $method): string {
        return ReportHelper::translatePaymentMethod($method);
    }
}
