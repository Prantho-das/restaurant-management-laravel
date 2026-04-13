@php
use App\Http\Controllers\ReportHelper;
$locale = $reportLocale ?? 'en';
ReportHelper::setLocale($locale);

function t(string $key): string {
    return ReportHelper::t($key);
}

function formatNum($num, int $decimals = 2): string {
    $locale = $GLOBALS['locale'] ?? 'en';
    $num = number_format((float) $num, $decimals);
    if ($locale === 'bn') {
        return ReportHelper::toBanglaNumbers($num);
    }
    return $num;
}

function formatDate($date, string $format = 'M d, Y'): string {
    return ReportHelper::formatDate($date, $format);
}

function tt(string $type): string {
    return ReportHelper::translateOrderType($type);
}

function tm(string $method): string {
    return ReportHelper::translatePaymentMethod($method);
}

$GLOBALS['locale'] = $locale;
$banglaFontPath = public_path('fonts/NotoSansBengali-Regular.ttf');
$banglaFontAvailable = file_exists($banglaFontPath);
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <style>
        @if ($banglaFontAvailable)
        @font-face {
            font-family: 'NotoSansBengali';
            font-style: normal;
            font-weight: 400;
            src: url('{{ $banglaFontPath }}') format('truetype');
        }
        @endif

        body {
            font-family: @if ($locale === 'bn' && $banglaFontAvailable)'NotoSansBengali', @endif 'DejaVu Sans', sans-serif;
            @if ($locale === 'bn')
            font-feature-settings: "liga" 1, "clig" 1;
            @endif
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4a5568;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #2d3748;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            color: #718096;
        }
        .lang-badge {
            float: right;
            background: #4a5568;
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            text-transform: uppercase;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #f7fafc;
            color: #4a5568;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mt-4 { margin-top: 1rem; }
        .bg-gray-100 { background-color: #f7fafc; }
        .text-success { color: #38a169; }
        .text-danger { color: #e53e3e; }
        .summary-box {
            background-color: #edf2f7;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .summary-box h3 {
            margin-top: 0;
            font-size: 16px;
            border-bottom: 1px solid #cbd5e0;
            padding-bottom: 5px;
        }
        .grid {
            display: table;
            width: 100%;
        }
        .col {
            display: table-cell;
            width: 50%;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <span class="lang-badge">{{ $locale === 'bn' ? 'বাংলা' : 'English' }}</span>
        <h1>{{ \App\Models\Setting::getValue('site_name', \App\Models\Setting::getValue('site_title', config('app.name'))) }}</h1>
        <p>@yield('report_name')</p>
        <p>{{ t('period') }}: {{ formatDate($startDate) }} - {{ formatDate($endDate) }}</p>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <div class="footer">
        {{ t('generated_on') }}: {{ formatDate(now(), 'M d, Y H:i:s') }} | {{ t('page') }} <span class="pagenum"></span>
    </div>
</body>
</html>
