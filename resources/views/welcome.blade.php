<x-layout>
    <x-slot:title>{{ $cms->hero_subtitle ?? 'Royal Dine' }} - Heritage Bangladeshi Cuisine</x-slot:title>

    <!-- Hero Section -->
    <section class="section-padding overflow-hidden relative min-h-[90vh] flex items-center">
        <!-- Subtle Pattern Background -->
        <div class="absolute inset-0 bg-subtle-pattern pointer-events-none"></div>
        
        <div class="container-wide grid lg:grid-cols-12 gap-16 items-center relative z-10">
            <!-- Text Content -->
            <div class="lg:col-span-6 animate-fade-in-up">
                <span class="inline-block text-brand-gold font-bold tracking-[0.4em] uppercase text-[10px] mb-6">{{ $cms->hero_subtitle ?? 'Authentic Traditions' }}</span>
                <h1 class="text-6xl md:text-8xl mb-10 leading-[0.9] text-brand-emerald">
                    {!! $cms->hero_title ?? 'Serving <span class="text-brand-gold italic">Royalty</span> Since Generations.' !!}
                </h1>
                <p class="text-lg text-brand-emerald/70 mb-12 max-w-lg leading-relaxed font-medium">
                    {{ $cms->hero_description ?? 'Experience the timeless flavors of Bangladesh.' }}
                </p>
                <div class="flex flex-wrap gap-6">
                    <a href="#menu" wire:navigate class="btn-brand">Explore Menu</a>
                    <a href="#reservation" wire:navigate class="btn-brand-outline">Book a Table</a>
                </div>
            </div>

            <!-- Image Composition -->
            <div class="lg:col-span-6 relative animate-fade-in-up delay-200">
                <div class="relative z-10 rounded-2xl overflow-hidden shadow-premium transform rotate-2 hover:rotate-0 transition-transform duration-700 aspect-square lg:aspect-[4/5]">
                    <img src="{{ !empty($cms->hero_image) ? Storage::url($cms->hero_image) : '/images/placeholders/restaurant_interior_1774629009066.png' }}" alt="Royal Dine Interior" class="w-full h-full object-cover">
                </div>
                <!-- Decorative Elements -->
                <div class="absolute -top-10 -right-10 w-40 h-40 border-2 border-brand-gold/20 rounded-full pointer-events-none"></div>
                <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-brand-gold/5 blur-3xl rounded-full pointer-events-none"></div>
            </div>
        </div>
    </section>

    <!-- Heritage Section -->
    <section class="section-padding bg-brand-emerald text-parchment relative overflow-hidden">
        <div class="absolute inset-0 bg-subtle-pattern opacity-5"></div>
        
        <div class="container-wide grid lg:grid-cols-2 gap-24 items-center relative z-10">
            <div class="order-2 lg:order-1 animate-fade-in-up">
                <div class="grid grid-cols-2 gap-6">
                    <div class="pt-12">
                         <img src="{{ !empty($cms->heritage_image_1) ? Storage::url($cms->heritage_image_1) : '/images/placeholders/kacchi_biryani_1774629083139.png' }}" class="rounded-2xl shadow-premium aspect-square object-cover mb-6 border border-white/10">
                         <img src="{{ !empty($cms->heritage_image_2) ? Storage::url($cms->heritage_image_2) : '/images/placeholders/bhuna_khichuri_beef_1774629196663.png' }}" class="rounded-2xl shadow-premium aspect-square object-cover border border-white/10">
                    </div>
                    <div>
                         <img src="{{ !empty($cms->heritage_image_3) ? Storage::url($cms->heritage_image_3) : '/images/placeholders/gallery_fuchka_1774630415473.png' }}" class="rounded-2xl shadow-premium aspect-[3/4] object-cover mb-6 border border-white/10">
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2 animate-fade-in-up delay-300">
                <h2 class="text-xs text-brand-gold font-black uppercase tracking-[0.5em] mb-8 font-sans">{{ $cms->heritage_subtitle ?? 'Our Secret' }}</h2>
                <h1 class="text-5xl md:text-6xl mb-10 leading-tight">
                    {!! $cms->heritage_title ?? 'Traditional Soul, Modern <span class="text-brand-gold">Craft.</span>' !!}
                </h1>
                <p class="text-parchment/60 text-lg leading-loose mb-12">
                    {{ $cms->heritage_description ?? 'Our recipes are secrets passed down through generations.' }}
                </p>
                <div class="flex flex-col gap-10">
                    <div class="flex gap-6 items-start">
                        <span class="text-3xl text-brand-gold font-serif">{{ $cms->secret_subtitle ?? '01.' }}</span>
                        <div>
                            <h4 class="text-xl font-bold font-sans uppercase tracking-widest mb-2">{{ $cms->secret_title ?? 'Heritage Spices' }}</h4>
                            <p class="text-sm text-parchment/50">{{ $cms->secret_description ?? 'Sourced directly from local farmers.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Signature Menu Section -->
    <section id="menu" class="section-padding bg-white relative">
        <div class="container-wide">
            <div class="text-center max-w-2xl mx-auto mb-24 animate-fade-in-up">
                <h2 class="text-xs text-brand-gold font-black uppercase tracking-[0.5em] mb-6 font-sans italic-none">Chef's Signature</h2>
                <h1 class="text-5xl md:text-6xl text-brand-emerald mb-8">The <span class="italic">Royal</span> Selection</h1>
                <p class="text-brand-emerald/60">A curated journey through the finest delicacies of Bengal.</p>
            </div>

            <livewire:frontend.featured-menu />

            <div class="mt-20 text-center animate-fade-in-up">
                <a href="/menu" wire:navigate class="btn-royal px-12 py-5 text-sm font-black tracking-[0.3em] uppercase inline-flex items-center gap-4">
                    Discover Entire Menu
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="section-padding bg-parchment">
        <div class="container-wide">
            <div class="flex flex-col md:flex-row justify-between items-end mb-24 animate-fade-in-up">
                <div class="max-w-xl">
                    <h2 class="text-xs text-brand-gold font-black uppercase tracking-[0.5em] mb-6 font-sans">{{ $cms->visual_story_subtitle ?? 'Visual Story' }}</h2>
                    <h1 class="text-5xl md:text-6xl text-brand-emerald">{!! $cms->visual_story_title ?? 'The <span class="italic">Atmosphere</span>' !!}</h1>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-in-up">
                <div class="col-span-2 row-span-2 rounded-3xl overflow-hidden shadow-premium group">
                    <img src="/images/placeholders/restaurant_interior_1774629009066.png" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                </div>
                <div class="rounded-3xl overflow-hidden shadow-premium aspect-square group">
                    <img src="/images/placeholders/kacchi_biryani_1774629083139.png" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                </div>
                <div class="rounded-3xl overflow-hidden shadow-premium aspect-square group">
                    <img src="/images/placeholders/gallery_mutton_curry_1774630607713.png" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                </div>
                <div class="col-span-2 rounded-3xl overflow-hidden shadow-premium aspect-[2/1] group">
                    <img src="/images/placeholders/bhuna_khichuri_beef_1774629196663.png" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                </div>
            </div>
        </div>
    </section>

    <!-- Reservation Section -->
    <section id="reservation" class="section-padding bg-brand-emerald relative overflow-hidden">
        <div class="absolute inset-0 bg-subtle-pattern opacity-5"></div>
        
        <div class="container-wide relative z-10">
            <div class="grid lg:grid-cols-12 gap-20 items-center">
                <div class="lg:col-span-5 text-parchment animate-fade-in-up">
                    <h2 class="text-[11px] font-black tracking-[0.5em] uppercase text-brand-gold mb-10">Private Booking</h2>
                    <h1 class="text-5xl md:text-7xl mb-10 leading-none">A Table <br> Reserved for <span class="italic text-brand-gold">You.</span></h1>
                    <p class="text-parchment/60 text-lg mb-12 leading-relaxed">Join us for an evening of quiet luxury. We accommodate private events and corporate gatherings.</p>
                </div>

                <div class="lg:col-span-7 animate-fade-in-up delay-200">
                    <div class="bg-parchment p-10 lg:p-16 rounded-3xl shadow-2xl relative border border-brand-gold/10">
                        <livewire:frontend.reservation-form />
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
