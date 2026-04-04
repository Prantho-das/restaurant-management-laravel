@php
    $logo = App\Models\Setting::getValue('site_logo');
    $siteName = App\Models\Setting::getValue('site_name', config('app.name'));
@endphp

@if($logo)
    <img src="{{ $logo ? Storage::url($logo) : asset('placeholder.png') }}" alt="{{ $siteName }}" class="h-12 w-auto object-contain">
@else
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded bg-primary-600 flex items-center justify-center text-white font-serif italic text-xl font-bold">
            {{ substr($siteName, 0, 1) }}
        </div>
        <span class="text-xl font-serif italic font-bold tracking-tight text-gray-950 dark:text-white">
            {{ $siteName }}
        </span>
    </div>
@endif
