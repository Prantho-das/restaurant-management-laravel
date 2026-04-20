@php
    $gtmId = \App\Models\Setting::where('key', 'gtm_id')->value('value');
    $isGa4 = str_starts_with($gtmId, 'G-');
@endphp

@if($gtmId)
    @if($isGa4)
        <!-- Google tag (gtag.js) GA4 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gtmId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $gtmId }}');
        </script>
    @else
        <!-- Google Tag Manager -->
        <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $gtmId }}');
        </script>
        <!-- End Google Tag Manager -->
    @endif
@endif