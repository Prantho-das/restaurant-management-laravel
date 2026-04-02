@php
    $cartCount = collect(session('cart', []))->sum('quantity');
@endphp
<nav x-data="{ open: false, scrolled: false, cartCount: {{ $cartCount }} }" 
     @cart-updated.window="cartCount = $event.detail.count"
     @scroll.window="scrolled = (window.pageYOffset > 50) ? true : false"
     :class="{ 'glass-light py-2 shadow-premium': scrolled, 'bg-transparent py-6': !scrolled }"
     class="fixed top-0 left-0 w-full z-50 transition-all duration-500">
     
    <div class="container-wide flex justify-between items-center">
        <!-- Logo -->
        <a href="/" wire:navigate class="flex items-center gap-3">
            @if($logo = App\Models\Setting::getValue('site_logo'))
                <img src="{{ Storage::url($logo) }}" alt="{{ App\Models\Setting::getValue('site_title', 'Royal Dine') }}" class="h-12 w-auto object-contain">
            @else
                <div class="flex flex-col">
                    <span class="text-brand-emerald font-serif italic text-2xl font-black tracking-tight leading-none uppercase">{{ App\Models\Setting::getValue('site_title', 'Royal Dine') }}</span>
                    <span class="text-[9px] text-brand-gold font-bold tracking-[0.2em] uppercase mt-1">Heritage Cuisine</span>
                </div>
            @endif
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center gap-12">
            <div class="flex items-center gap-10">
                <a href="/" wire:navigate class="text-xs font-bold uppercase tracking-widest hover:text-brand-gold transition-colors">Home</a>
                <a href="/#menu" wire:navigate class="text-xs font-bold uppercase tracking-widest hover:text-brand-gold transition-colors">Menu</a>
                <a href="/#reservation" wire:navigate class="text-xs font-bold uppercase tracking-widest hover:text-brand-gold transition-colors">Book a Table</a>
            </div>
            
            <div class="h-8 w-px bg-brand-gold/20 mx-2"></div>
            
            <a href="/order" wire:navigate class="btn-brand text-xs tracking-widest uppercase relative">
                Order Online
                <span x-show="cartCount > 0"
                      x-text="cartCount"
                      x-transition:enter="transition ease-out duration-200"
                      x-transition:enter-start="scale-0 opacity-0"
                      x-transition:enter-end="scale-100 opacity-100"
                      class="absolute -top-2 -right-2 bg-brand-red text-white text-[10px] font-bold min-w-5 h-5 px-1 rounded-full flex items-center justify-center shadow-md"
                      style="display: none;"></span>
            </a>
        </div>

        <!-- Mobile Contact/Action (Optional) -->
        <div class="md:hidden flex items-center gap-4">
            <a href="tel:{{ App\Models\Setting::getValue('site_phone') }}" class="text-brand-emerald">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
            </a>
            <button @click="open = !open" class="text-brand-emerald">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-10"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="md:hidden bg-parchment h-screen fixed inset-0 z-40 flex flex-col items-center justify-center gap-12 text-center">
        <button @click="open = false" class="absolute top-8 right-8 text-brand-emerald">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <a @click="open = false" href="/" wire:navigate class="font-serif italic text-5xl hover:text-brand-gold transition-colors">Home</a>
        <a @click="open = false" href="/#menu" wire:navigate class="font-serif italic text-5xl hover:text-brand-gold transition-colors">Our Menu</a>
        <a @click="open = false" href="/#reservation" wire:navigate class="font-serif italic text-5xl hover:text-brand-gold transition-colors">Book a Table</a>
        <a @click="open = false" href="/order" wire:navigate class="btn-brand text-xl px-12 py-5 relative">
            Order Online
            <span x-show="cartCount > 0"
                  x-text="cartCount"
                  class="absolute -top-2 -right-3 bg-brand-red text-white text-xs font-bold min-w-6 h-6 px-1 rounded-full flex items-center justify-center shadow-md"
                  style="display: none;"></span>
        </a>
    </div>
</nav>
