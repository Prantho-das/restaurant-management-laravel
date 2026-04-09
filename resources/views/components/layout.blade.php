<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        $siteTitle = App\Models\Setting::getValue('site_title', 'Royal Dine') . ' - ' . App\Models\Setting::getValue('site_subtitle', 'Premium Heritage Cuisine');
        $siteDescription = App\Models\Setting::getValue('site_description');
        $siteKeywords = App\Models\Setting::getValue('site_keywords');
        $siteFavicon = App\Models\Setting::getValue('site_favicon');
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @if($siteDescription)
        <meta name="description" content="{{ $siteDescription }}">
    @endif
    
    @if($siteKeywords)
        <meta name="keywords" content="{{ $siteKeywords }}">
    @endif

    @if($siteFavicon)
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($siteFavicon) }}">
    @endif

    <title>{{ $title ?? $siteTitle }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <x-marketing.gtm-head />
    <x-marketing.meta-pixel />
</head>
<body class="antialiased">
    <x-marketing.gtm-body />
    <x-navbar />

    <main class="min-h-screen pt-16 md:pt-20">
        {{ $slot }}
    </main>

    <x-mobile-bottom-nav />

    <x-footer />

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then((registration) => {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, (err) => {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
    <x-notifications />
</body>

</html>
