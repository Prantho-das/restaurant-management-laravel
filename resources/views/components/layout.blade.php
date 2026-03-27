<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        $siteTitle = App\Models\Setting::getValue('site_title', 'Royal Dine - Premium Heritage Cuisine');
        $siteDescription = App\Models\Setting::getValue('site_description');
        $siteKeywords = App\Models\Setting::getValue('site_keywords');
        $siteFavicon = App\Models\Setting::getValue('site_favicon');
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Outfit:wght@100..900&family=Hind+Siliguri:wght@300;400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <x-marketing.meta-pixel />
</head>
<body class="antialiased">
    <x-navbar />

    <main class="min-h-screen">
        {{ $slot }}
    </main>

    <x-footer />
    
    <!-- Alpine JS for interactivity -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
