@php
    $cartCount = collect(session('cart', []))->sum('quantity');
    $categories = \App\Models\Category::where('is_active', true)->whereNull('parent_id')->orderBy('priority_order')->get();
    $fbUrl = \App\Models\Setting::getValue('social_facebook_url');
    $igUrl = \App\Models\Setting::getValue('social_instagram_url');
@endphp
<nav x-data="{
        open: false,
        scrolled: false,
        cartCount: {{ $cartCount }},
        init() {
            this.scrolled = window.scrollY > 20;
            this.$watch('open', val => {
                document.body.style.overflow = val ? 'hidden' : '';
            });
        }
     }"
     @scroll.window="scrolled = window.scrollY > 20"
     @cart-updated.window="cartCount = $event.detail.count"
     :class="scrolled || open ? 'bg-white shadow-lg' : 'bg-white'"
     class="fixed top-0 left-0 w-full z-50 py-3 md:py-4 transition-all duration-300">

    <div class="container-wide flex justify-between items-center">
        <!-- Logo -->
        <a href="/" wire:navigate class="flex items-center gap-3 group relative z-25">
            @if($logo = App\Models\Setting::getValue('site_logo'))
                <img src="{{ Storage::url($logo) }}" alt="{{ App\Models\Setting::getValue('site_title', 'Kacchi Bhai') }}" class="h-10 md:h-14 w-auto object-contain">
            @else
                <span class="text-[#c01c1c] font-black text-xl md:text-2xl uppercase">{{ App\Models\Setting::getValue('site_title', 'Kacchi Bhai') }}</span>
            @endif
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center gap-8">
            <a href="/" wire:navigate class="text-sm font-bold uppercase tracking-wider hover:text-[#c01c1c] {{ request()->routeIs('home') ? 'text-[#c01c1c]' : 'text-[#333333]' }}">হোম</a>
            <a href="{{ url('/menu') }}" wire:navigate class="text-sm font-bold uppercase tracking-wider hover:text-[#c01c1c] {{ request()->routeIs('menu') ? 'text-[#c01c1c]' : 'text-[#333333]' }}">মেনু</a>
            <a href="{{ url('/') }}#reservation" class="text-sm font-bold uppercase tracking-wider hover:text-[#c01c1c] text-[#333333]">বুকিং</a>
            
            <a href="/order" wire:navigate class="bg-[#c01c1c] text-white px-5 py-2 rounded-lg text-sm font-bold uppercase hover:bg-[#d92e2e]">
                অর্ডার করুন
            </a>
        </div>

        <!-- Mobile Actions -->
        <div class="md:hidden flex items-center gap-2">
            <a href="/order" wire:navigate class="relative text-[#333333] p-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
            </a>
            <button @click="open = !open" class="w-10 h-10 flex items-center justify-center text-[#333333]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    @if(false)
    <div x-show="open" class="md:hidden bg-white border-t">
        <div class="container py-4 space-y-3">
            <a href="/" class="block py-2 text-[#333333] font-bold">হোম</a>
            <a href="/menu" class="block py-2 text-[#333333] font-bold">মেনু</a>
            <a href="#reservation" class="block py-2 text-[#333333] font-bold">বুকিং</a>
            <a href="/order" class="block py-2 text-[#c01c1c] font-bold">অর্ডার করুন</a>
        </div>
    </div>
    @endif
</nav>