<div class="homepage-wrapper">
    <x-slot:title>{{ $cms->hero_subtitle ?? 'Royal Dine' }} - Heritage Bangladeshi Cuisine</x-slot:title>

    <!-- Hero Section -->
    <section class="section-padding relative min-h-[90vh] flex items-center overflow-hidden">
        <!-- Subtle Pattern Background -->
        <div class="absolute inset-0 bg-subtle-pattern pointer-events-none"></div>
        
        <!-- Decorative Elements -->
        <div class="absolute top-20 right-0 w-[40%] h-[60%] bg-gradient-to-l from-brand-gold/5 to-transparent rounded-l-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-[30%] h-[40%] bg-gradient-to-r from-brand-emerald/5 to-transparent rounded-r-full blur-3xl"></div>
        
        <div class="container-wide grid lg:grid-cols-12 gap-12 lg:gap-20 items-center relative z-10">
            <!-- Text Content -->
            <div class="lg:col-span-6">
                <span class="inline-block text-brand-gold font-bold tracking-[0.4em] uppercase text-[10px] mb-6 animate-fade-in-up">.{{ $cms->hero_subtitle ?? 'Authentic Traditions' }}</span>
                <h1 class="text-5xl md:text-7xl lg:text-8xl mb-10 leading-[0.9] text-brand-emerald animate-fade-in-up delay-100">
                    {!! $cms->hero_title ?? 'Serving <span class="italic text-brand-gold">Royalty</span> Since Generations.' !!}
                </h1>
                <p class="text-lg text-brand-emerald/70 mb-12 max-w-lg leading-relaxed font-medium animate-fade-in-up delay-200">
                    {{ $cms->hero_description ?? 'Experience the timeless flavors of Bangladesh.' }}
                </p>
                <div class="flex flex-wrap gap-4 lg:gap-6 animate-fade-in-up delay-300">
                    <a href="#menu" wire:navigate class="btn-emerald">Explore Menu</a>
                    <a href="#reservation" wire:navigate class="btn-brand-outline border-brand-emerald text-brand-emerald hover:bg-brand-emerald hover:text-white">Book a Table</a>
                </div>
            </div>

            <!-- Image Composition -->
            <div class="lg:col-span-6 relative">
                <div class="relative z-10 rounded-3xl overflow-hidden shadow-luxury transform hover:scale-[1.02] transition-transform duration-700 aspect-square lg:aspect-[4/5] animate-fade-in-scale delay-200">
                    <img src="{{ !empty($cms->hero_image) ? Storage::url($cms->hero_image) : asset('placeholder.png') }}" alt="Royal Dine Interior" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-emerald/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-500"></div>
                </div>
                <!-- Decorative Elements -->
                <div class="absolute -top-8 -right-8 w-32 h-32 border border-brand-gold/20 rounded-full animate-float"></div>
                <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-brand-gold/5 blur-3xl rounded-full"></div>
                <div class="absolute top-1/2 -left-8 w-16 h-16 bg-brand-gold/10 rounded-2xl rotate-12"></div>
            </div>
        </div>
    </section>

    <!-- Heritage Section -->
    <section class="section-padding bg-brand-emerald text-parchment relative overflow-hidden">
        <div class="absolute inset-0 bg-subtle-pattern opacity-5"></div>
        <div class="absolute top-0 right-0 w-[50%] h-full bg-gradient-to-l from-brand-gold/5 to-transparent"></div>
        
        <div class="container-wide grid lg:grid-cols-2 gap-16 lg:gap-24 items-center relative z-10">
            <div class="order-2 lg:order-1">
                <div class="grid grid-cols-2 gap-4 lg:gap-6">
                    <div class="pt-8 lg:pt-12">
                        <img src="{{ !empty($cms->heritage_image_1) ? Storage::url($cms->heritage_image_1) : asset('placeholder.png') }}" class="rounded-2xl lg:rounded-3xl shadow-luxury aspect-square object-cover mb-4 lg:mb-6 border border-white/10 hover:scale-[1.02] transition-transform duration-500">
                        <img src="{{ !empty($cms->heritage_image_2) ? Storage::url($cms->heritage_image_2) : asset('placeholder.png') }}" class="rounded-2xl lg:rounded-3xl shadow-luxury aspect-square object-cover border border-white/10 hover:scale-[1.02] transition-transform duration-500">
                    </div>
                    <div>
                        <img src="{{ !empty($cms->heritage_image_3) ? Storage::url($cms->heritage_image_3) : asset('placeholder.png') }}" class="rounded-2xl lg:rounded-3xl shadow-luxury aspect-[3/4] object-cover mb-4 lg:mb-6 border border-white/10 hover:scale-[1.02] transition-transform duration-500">
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2">
                <h2 class="text-xs text-brand-gold font-black uppercase tracking-[0.5em] mb-8 font-sans">{{ $cms->heritage_subtitle ?? 'Our Secret' }}</h2>
                <h1 class="text-4xl md:text-5xl lg:text-6xl mb-10 leading-tight">
                    {!! $cms->heritage_title ?? 'Traditional Soul, Modern <span class="text-brand-gold italic">Craft.</span>' !!}
                </h1>
                <p class="text-parchment/60 text-base lg:text-lg leading-loose mb-12">
                    {{ $cms->heritage_description ?? 'Our recipes are secrets passed down through generations.' }}
                </p>
                <div class="flex flex-col gap-8 lg:gap-10">
                    <div class="flex gap-4 lg:gap-6 items-start group">
                        <span class="text-3xl lg:text-4xl text-brand-gold font-serif group-hover:scale-110 transition-transform duration-300">{{ $cms->secret_subtitle ?? '01.' }}</span>
                        <div>
                            <h4 class="text-lg lg:text-xl font-bold font-sans uppercase tracking-widest mb-2">{{ $cms->secret_title ?? 'Heritage Spices' }}</h4>
                            <p class="text-sm text-parchment/50">{{ $cms->secret_description ?? 'Sourced directly from local farmers.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Signature Menu Section -->
    <section id="menu" class="section-padding bg-parchment relative">
        <div class="absolute top-20 left-0 w-[30%] h-[40%] bg-brand-emerald/5 rounded-full blur-3xl"></div>
        
        <div class="container-wide">
            <div class="text-center max-w-2xl mx-auto mb-16 lg:mb-24">
                <span class="inline-block text-brand-gold font-bold tracking-[0.5em] uppercase text-[10px] mb-6 lg:mb-8">Chef's Signature</span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl text-brand-emerald mb-6 lg:mb-8">The <span class="italic text-brand-gold">Royal</span> Selection</h1>
                <p class="text-brand-emerald/60 text-base lg:text-lg">A curated journey through the finest delicacies of Bengal.</p>
            </div>

            <livewire:frontend.featured-menu />

            <div class="mt-16 lg:mt-20 text-center">
                <a href="/menu" wire:navigate class="btn-royal px-10 lg:px-14 py-4 lg:py-5 text-sm font-black tracking-[0.3em] uppercase inline-flex items-center gap-4 group">
                    <span>Discover Entire Menu</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 group-hover:translate-x-2 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="section-padding bg-white">
        <div class="container-wide">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 lg:mb-20">
                <div class="max-w-xl">
                    <span class="inline-block text-brand-gold font-bold tracking-[0.5em] uppercase text-[10px] mb-6">{{ $cms->visual_story_subtitle ?? 'Visual Story' }}</span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl text-brand-emerald">{!! $cms->visual_story_title ?? 'The <span class="italic">Atmosphere</span>' !!}</h1>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                <div class="col-span-2 row-span-2 rounded-2xl lg:rounded-3xl overflow-hidden shadow-card group">
                    <img src="{{ asset('placeholder.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                </div>
                <div class="rounded-2xl lg:rounded-3xl overflow-hidden shadow-card aspect-square group">
                    <img src="{{ asset('placeholder.png') }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="rounded-2xl lg:rounded-3xl overflow-hidden shadow-card aspect-square group">
                    <img src="{{ asset('placeholder.png') }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="col-span-2 rounded-2xl lg:rounded-3xl overflow-hidden shadow-card aspect-[2/1] group">
                    <img src="{{ asset('placeholder.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                </div>
            </div>
        </div>
    </section>

    <!-- Reservation Section -->
    <section id="reservation" class="section-padding bg-brand-emerald relative overflow-hidden">
        <div class="absolute inset-0 bg-subtle-pattern opacity-5"></div>
        <div class="absolute top-0 right-0 w-[40%] h-full bg-gradient-to-l from-brand-gold/5 to-transparent"></div>
        <div class="absolute bottom-0 left-0 w-[30%] h-[30%] bg-brand-gold/10 rounded-full blur-3xl"></div>
        
        <div class="container-wide relative z-10">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-20 items-center">
                <div class="lg:col-span-5 text-parchment">
                    <span class="inline-block text-[11px] font-black tracking-[0.5em] uppercase text-brand-gold mb-8 lg:mb-10">Private Booking</span>
                    <h1 class="text-4xl md:text-5xl lg:text-7xl mb-8 lg:mb-10 leading-none">A Table <br> Reserved for <span class="italic text-brand-gold">You.</span></h1>
                    <p class="text-parchment/60 text-base lg:text-lg mb-10 lg:mb-12 leading-relaxed">Join us for an evening of quiet luxury. We accommodate private events and corporate gatherings.</p>
                    
                    <!-- Contact Cards -->
                    <div class="flex flex-col gap-6">
                        <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-colors duration-300">
                            <div class="w-12 h-12 bg-brand-gold/20 rounded-full flex items-center justify-center text-brand-gold">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-widest text-brand-gold mb-1">VIP Concierge</h4>
                                <p class="text-sm text-parchment/70">{{ App\Models\Setting::getValue('footer_phone', '+880 1234 567890') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <div class="bg-parchment p-8 lg:p-12 xl:p-16 rounded-3xl shadow-luxury relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-brand-gold/10 blur-3xl rounded-full"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-brand-emerald/5 blur-3xl rounded-full"></div>
                        
                        <livewire:frontend.reservation-form />
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>