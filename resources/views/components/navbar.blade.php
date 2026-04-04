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
            window.addEventListener('scroll', () => {
                if (!this.open) {
                    this.scrolled = window.pageYOffset > 50;
                }
            });
            this.$watch('open', val => {
                document.body.style.overflow = val ? 'hidden' : '';
            });
        }
     }"
     @cart-updated.window="cartCount = $event.detail.count"
     :class="{ 'glass-light shadow-lg shadow-brand-emerald/10': scrolled && !open, 'bg-transparent': !scrolled && !open, 'glass-light shadow-lg shadow-brand-emerald/10': open }"
     class="fixed top-0 left-0 w-full z-50 py-4 md:py-6 transition-all duration-300">

    <div class="container-wide flex justify-between items-center">
        <!-- Logo -->
        <a href="/" wire:navigate class="flex items-center gap-3 group relative z-25">
            @if($logo = App\Models\Setting::getValue('site_logo'))
                <img src="{{ Storage::url($logo) }}" alt="{{ App\Models\Setting::getValue('site_title', 'Royal Dine') }}" class="h-10 md:h-12 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
            @else
                <div class="flex flex-col">
                    <span class="text-brand-emerald font-serif italic text-xl md:text-2xl font-black tracking-tight leading-none uppercase group-hover:text-brand-emerald-light transition-colors">{{ App\Models\Setting::getValue('site_title', 'Royal Dine') }}</span>
                    <span class="text-[8px] md:text-[9px] text-brand-gold font-bold tracking-[0.2em] uppercase mt-0.5 md:mt-1">{{ App\Models\Setting::getValue('site_subtitle', 'Heritage Cuisine') }}</span>
                </div>
            @endif
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center gap-10 lg:gap-12">
            <div class="flex items-center gap-8 lg:gap-10">
                <a href="/" wire:navigate 
                   class="text-xs font-bold uppercase tracking-widest hover:text-brand-gold transition-colors duration-300 relative after:absolute after:bottom-[-4px] after:left-0 after:h-0.5 after:bg-brand-gold after:transition-all after:duration-300 {{ request()->routeIs('home') ? 'text-brand-gold after:w-full' : 'after:w-0' }}">Home</a>
                <a href="{{ url('/menu') }}" wire:navigate 
                   class="text-xs font-bold uppercase tracking-widest hover:text-brand-gold transition-colors duration-300 relative after:absolute after:bottom-[-4px] after:left-0 after:h-0.5 after:bg-brand-gold after:transition-all after:duration-300 {{ request()->routeIs('menu') ? 'text-brand-gold after:w-full' : 'after:w-0' }}">Menu</a>
                <a href="{{ url('/') }}#reservation" 
                   class="text-xs font-bold uppercase tracking-widest hover:text-brand-gold transition-colors duration-300 relative after:absolute after:bottom-[-4px] after:left-0 after:w-0 after:h-0.5 after:bg-brand-gold after:transition-all after:duration-300 hover:after:w-full">Book a Table</a>
            </div>

            <div class="h-8 w-px bg-brand-gold/20 mx-1"></div>

            <a href="/order" wire:navigate class="btn-brand text-xs tracking-widest uppercase relative group {{ request()->routeIs('order') ? 'opacity-90' : '' }}">
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

        <!-- Mobile Actions -->
        <div class="md:hidden flex items-center gap-1 relative z-50">
            <!-- Cart Icon Mobile -->
            <a href="/order" wire:navigate class="relative text-brand-emerald p-2.5 rounded-full hover:bg-brand-emerald/10 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                <span x-show="cartCount > 0"
                      x-text="cartCount"
                      class="absolute -top-0.5 -right-0.5 bg-brand-red text-white text-[9px] font-bold min-w-4 h-4 px-0.5 rounded-full flex items-center justify-center">
                </span>
            </a>
            <!-- Hamburger / Close Button -->
            <button @click="open = !open"
                    class="relative w-10 h-10 rounded-full flex items-center justify-center text-brand-emerald hover:bg-brand-emerald/10 transition-colors">
                <!-- Hamburger -->
                <span class="absolute inset-0 flex items-center justify-center transition-all duration-300"
                      :class="open ? 'opacity-0 rotate-90 scale-50' : 'opacity-100 rotate-0 scale-100'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </span>
                <!-- Close X -->
                {{-- <span class="absolute inset-0 flex items-center justify-center transition-all duration-300"
                      :class="open ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 -rotate-90 scale-50'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </span> --}}
            </button>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div x-show="open"
         x-cloak
         class="md:hidden fixed inset-0 z-40"
         style="top: 0;">

        <!-- Clickable Backdrop -->
        <div class="absolute inset-0 bg-brand-emerald/60 backdrop-blur-sm"
             @click="open = false"
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>

        <!-- Decorative blobs on backdrop -->
        <div class="absolute top-20 -right-20 w-52 h-52 bg-brand-gold/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-40 -left-16 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Sliding Panel -->
        <div class="fixed top-0 right-0 h-[100vh] w-[85%] max-w-sm flex flex-col bg-parchment shadow-2xl"
             x-transition:enter="transition ease-out duration-350"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-250"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             style="overscroll-behavior: contain;">

            <!-- Decorative accent bar at top -->
            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-brand-gold via-brand-emerald to-brand-gold opacity-60 rounded-r"></div>

            <!-- Panel Header -->
            <div class="flex items-center justify-between px-6 pt-6 pb-5 border-b border-brand-emerald/10 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-brand-emerald to-brand-emerald/80 rounded-xl flex items-center justify-center shadow-md">
                        <span class="text-white font-serif italic text-lg font-black">R</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-serif italic text-lg text-brand-emerald font-black leading-tight">{{ App\Models\Setting::getValue('site_title', 'Royal Dine') }}</span>
                        <span class="text-[8px] text-brand-gold font-bold tracking-[0.2em] uppercase">{{ App\Models\Setting::getValue('site_subtitle', 'Heritage Cuisine') }}</span>
                    </div>
                </div>
                <button @click="open = false"
                        class="w-9 h-9 rounded-full border border-brand-emerald/20 flex items-center justify-center text-brand-emerald hover:bg-brand-emerald hover:text-white hover:border-brand-emerald transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto overscroll-contain py-6 px-4 space-y-1">

                <!-- Section label -->
                <p class="text-[9px] font-black uppercase tracking-[0.3em] text-brand-gold/70 px-4 mb-3">Navigation</p>

                <a @click="open = false" href="/" wire:navigate
                   class="flex items-center gap-4 py-3.5 px-4 rounded-2xl transition-all duration-200 group {{ request()->routeIs('home') ? 'bg-brand-emerald text-white shadow-lg shadow-brand-emerald/20' : 'text-brand-emerald hover:text-brand-gold hover:bg-brand-emerald/5' }}">
                    <span class="w-10 h-10 rounded-xl bg-brand-emerald/8 border border-brand-emerald/10 flex items-center justify-center shrink-0 group-hover:bg-brand-emerald group-hover:border-brand-emerald group-hover:text-white transition-all duration-200 {{ request()->routeIs('home') ? 'bg-white/20 border-white/20 text-white' : '' }}">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </span>
                    <span class="font-serif italic text-lg font-bold">Home</span>
                </a>

                <a @click="open = false" href="{{ url('/menu') }}" wire:navigate
                   class="flex items-center gap-4 py-3.5 px-4 rounded-2xl transition-all duration-200 group {{ request()->routeIs('menu') ? 'bg-brand-emerald text-white shadow-lg shadow-brand-emerald/20' : 'text-brand-emerald hover:text-brand-gold hover:bg-brand-emerald/5' }}">
                    <span class="w-10 h-10 rounded-xl bg-brand-emerald/8 border border-brand-emerald/10 flex items-center justify-center shrink-0 group-hover:bg-brand-emerald group-hover:border-brand-emerald group-hover:text-white transition-all duration-200 {{ request()->routeIs('menu') ? 'bg-white/20 border-white/20 text-white' : '' }}">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </span>
                    <span class="font-serif italic text-lg font-bold">Our Menu</span>
                </a>

                <a @click="open = false" href="{{ url('/') }}#reservation"
                   class="flex items-center gap-4 py-3.5 px-4 rounded-2xl text-brand-emerald hover:text-brand-gold hover:bg-brand-emerald/5 active:bg-brand-emerald/10 transition-all duration-200 group">
                    <span class="w-10 h-10 rounded-xl bg-brand-emerald/8 border border-brand-emerald/10 flex items-center justify-center shrink-0 group-hover:bg-brand-emerald group-hover:border-brand-emerald group-hover:text-white transition-all duration-200">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <span class="font-serif italic text-lg font-bold">Book a Table</span>
                </a>

                <a @click="open = false" href="/order" wire:navigate
                   class="flex items-center gap-4 py-3.5 px-4 rounded-2xl transition-all duration-200 group {{ request()->routeIs('order') ? 'bg-brand-emerald text-white shadow-lg shadow-brand-emerald/20' : 'text-brand-emerald hover:text-brand-gold hover:bg-brand-emerald/5' }}">
                    <span class="w-10 h-10 rounded-xl bg-brand-emerald/8 border border-brand-emerald/10 flex items-center justify-center shrink-0 group-hover:bg-brand-emerald group-hover:border-brand-emerald group-hover:text-white transition-all duration-200 relative {{ request()->routeIs('order') ? 'bg-white/20 border-white/20 text-white' : '' }}">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span x-show="cartCount > 0" x-text="cartCount"
                              class="absolute -top-1.5 -right-1.5 bg-brand-red text-white text-[8px] font-bold min-w-[18px] h-[18px] px-0.5 rounded-full flex items-center justify-center shadow-sm">
                        </span>
                    </span>
                    <span class="font-serif italic text-lg font-bold">Order Online</span>
                </a>

                @if($categories->count() > 0)
                    <!-- Categories Sub-menu -->
                    <div class="pt-8 pb-3 px-4">
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-px bg-brand-emerald/10"></div>
                            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-brand-gold/60">Explore Menu</span>
                            <div class="flex-1 h-px bg-brand-emerald/10"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 px-2">
                        @foreach($categories as $category)
                            <a @click="open = false" 
                               href="{{ url('/menu') }}?selectedCategoryId={{ $category->id }}" 
                               wire:navigate
                               class="flex flex-col items-center gap-2 p-3 rounded-2xl border border-brand-emerald/5 hover:border-brand-gold/20 hover:bg-brand-gold/5 transition-all group">
                                <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-brand-emerald/10 group-hover:border-brand-gold/30 transition-colors">
                                    <img src="{{ !empty($category->image) ? Storage::url($category->image) : asset('placeholder.png') }}" 
                                         alt="{{ $category->name }}" 
                                         class="w-full h-full object-cover">
                                </div>
                                <span class="text-[10px] font-bold text-center text-brand-emerald/80 truncate w-full group-hover:text-brand-gold">{{ $category->name }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif

                <!-- User/Admin Section -->
                @auth
                    <div class="pt-8 pb-3 px-4">
                        <p class="text-[9px] font-black uppercase tracking-[0.3em] text-brand-gold/70 mb-1">Account</p>
                    </div>
                    <a @click="open = false" href="/admin"
                       class="flex items-center gap-4 py-3.5 px-4 rounded-2xl text-brand-emerald hover:text-brand-gold hover:bg-brand-emerald/5 transition-all group">
                        <span class="w-10 h-10 rounded-xl bg-brand-gold/10 border border-brand-gold/20 flex items-center justify-center shrink-0 group-hover:bg-brand-gold group-hover:text-white transition-all">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        <span class="font-serif italic text-lg font-bold">Admin Dashboard</span>
                    </a>
                @endauth

                <!-- Divider -->
                <div class="pt-8 pb-3 px-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-px bg-brand-emerald/10"></div>
                        <span class="text-[9px] font-black uppercase tracking-[0.3em] text-brand-gold/60">Contact</span>
                        <div class="flex-1 h-px bg-brand-emerald/10"></div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="px-4 space-y-2 pb-10">
                    @if($phone = App\Models\Setting::getValue('footer_phone'))
                        <a href="tel:{{ $phone }}"
                           class="flex items-center gap-3 p-3 rounded-xl hover:bg-brand-emerald/5 transition-colors group">
                            <span class="w-8 h-8 rounded-lg bg-brand-emerald/10 flex items-center justify-center text-brand-emerald group-hover:bg-brand-emerald group-hover:text-white transition-colors shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </span>
                            <span class="text-xs font-semibold text-brand-emerald/80 tracking-wide">{{ $phone }}</span>
                        </a>
                    @endif

                    @if($address = App\Models\Setting::getValue('footer_address'))
                        <div class="flex items-center gap-3 p-3 rounded-xl">
                            <span class="w-8 h-8 rounded-lg bg-brand-emerald/10 flex items-center justify-center text-brand-emerald shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <span class="text-xs font-semibold text-brand-emerald/80 leading-snug tracking-wide">{{ $address }}</span>
                        </div>
                    @endif

                    <!-- Social Media Links -->
                    <div class="flex items-center gap-4 pt-4 px-3">
                        @if($fbUrl)
                            <a href="{{ $fbUrl }}" target="_blank" class="w-10 h-10 rounded-full bg-brand-emerald/5 flex items-center justify-center text-brand-emerald hover:bg-brand-emerald hover:text-white transition-all shadow-sm">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                            </a>
                        @endif
                        @if($igUrl)
                            <a href="{{ $igUrl }}" target="_blank" class="w-10 h-10 rounded-full bg-brand-emerald/5 flex items-center justify-center text-brand-emerald hover:bg-brand-emerald hover:text-white transition-all shadow-sm">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </nav>

            <!-- Panel Footer CTA -->
            <div class="shrink-0 p-5 border-t border-brand-emerald/10 bg-gradient-to-r from-white/60 to-white/30 backdrop-blur-sm">
                <a href="/order" @click="open = false" wire:navigate
                   class="btn-emerald w-full text-center flex items-center justify-center gap-2.5 py-3.5 rounded-2xl font-bold text-sm tracking-widest uppercase shadow-lg shadow-brand-emerald/20 hover:shadow-xl hover:shadow-brand-emerald/30 transition-shadow duration-300">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Order Now
                </a>
                <p class="text-[9px] text-center text-brand-emerald/40 mt-3 uppercase tracking-[0.25em]">Open daily · 12pm – 11pm</p>
            </div>
        </div>
    </div>
</nav>