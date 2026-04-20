@php
    $gtmId = \App\Models\Setting::where('key', 'gtm_id')->value('value');
    $isGa4 = str_starts_with($gtmId, 'G-');
@endphp

@if($gtmId)
@unless($isGa4)
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@endunless
@endif