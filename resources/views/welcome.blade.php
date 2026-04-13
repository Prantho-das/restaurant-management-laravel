<div class="homepage-wrapper">
    <x-slot:title>{{ $cms->hero_subtitle ?? 'Kacchi Bhai' }} - Authentic Bangladeshi Cuisine</x-slot:title>

    <!-- Hero Section - Kacchi Bhai Style (White/Red) -->
    <section class="relative min-h-[90vh] flex items-center bg-[#fafafa]">
        <div class="container-wide grid lg:grid-cols-12 gap-8 lg:gap-12 items-center py-20">
            <!-- Content - Left Side -->
            <div class="lg:col-span-6 order-2 lg:order-1 text-center lg:text-left">
                <div class="flex items-center justify-center lg:justify-start gap-3 mb-4">
                    <div class="h-px w-12 bg-[#c01c1c]"></div>
                    <span class="text-[#c01c1c] font-bold tracking-[0.3em] uppercase text-sm">{{ $cms->hero_subtitle ??
                        'ঐতিহ্যবাহী স্বাদ' }}</span>
                    <div class="h-px w-12 bg-[#c01c1c]"></div>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black mb-6 leading-tight text-[#333333]">
                    {!! $cms->hero_title ?? '<span class="text-[#c01c1c]">কাচ্চি</span> ভাই' !!}
                </h1>

                <p class="text-[#666666] text-lg md:text-xl mb-8 leading-relaxed max-w-xl mx-auto lg:mx-0">
                    {{ $cms->hero_description ?? 'আমাদের ঐতিহ্যবাহী কাচ্চি সর্বদা আপনার সেবায়। সেরা মশলা, সেরা স্বাদ।'
                    }}
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="#menu" wire:navigate
                        class="bg-[#c01c1c] text-white px-10 py-4 rounded-lg font-bold tracking-wide transition-all duration-300 hover:bg-[#d92e2e] hover:shadow-lg">
                        মেনু দেখুন
                    </a>
                    <a href="#reservation" wire:navigate
                        class="border-2 border-[#c01c1c] text-[#c01c1c] px-10 py-4 rounded-lg font-bold tracking-wide transition-all duration-300 hover:bg-[#c01c1c] hover:text-white">
                        বুক করুন
                    </a>
                </div>

                <!-- Stats -->
                <div class="flex flex-wrap gap-8 mt-12 justify-center lg:justify-start">
                    <div class="text-center">
                        <span class="block text-3xl font-black text-[#c01c1c]">{{ $cms->stats_experience ?? '৫০+'
                            }}</span>
                        <span class="text-[#666666] text-sm">{{ $cms->stats_experience_label ?? 'বছর অভিজ্ঞতা' }}</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-3xl font-black text-[#c01c1c]">{{ $cms->stats_foods ?? '১০০+' }}</span>
                        <span class="text-[#666666] text-sm">{{ $cms->stats_foods_label ?? 'খাবার' }}</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-3xl font-black text-[#c01c1c]">{{ $cms->stats_customers ?? '৫০০০+'
                            }}</span>
                        <span class="text-[#666666] text-sm">{{ $cms->stats_customers_label ?? 'গ্রাহক' }}</span>
                    </div>
                </div>
            </div>

            <!-- Image - Right Side -->
            <div class="lg:col-span-6 order-1 lg:order-2">
                <div class="relative">
                    <div class="rounded-2xl overflow-hidden shadow-2xl border-4 border-[#c01c1c]/20">
                        <img src="{{ !empty($cms->hero_image) ? Storage::url($cms->hero_image) : asset('placeholder.png') }}"
                            alt="Kacchi Bhai Special" class="w-full h-[500px] lg:h-[600px] object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Heritage Section -->
    <section class="section-padding bg-white relative overflow-hidden">
        <div class="container-wide grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div class="order-2 lg:order-1">
                <div class="grid grid-cols-2 gap-4">
                    <div class="pt-8">
                        <img src="{{ !empty($cms->heritage_image_1) ? Storage::url($cms->heritage_image_1) : asset('placeholder.png') }}"
                            class="rounded-xl shadow-lg aspect-square object-cover mb-4 border-2 border-[#c01c1c]/20 hover:scale-[1.02] transition-transform duration-500">
                        <img src="{{ !empty($cms->heritage_image_2) ? Storage::url($cms->heritage_image_2) : asset('placeholder.png') }}"
                            class="rounded-xl shadow-lg aspect-square object-cover border-2 border-[#c01c1c]/20 hover:scale-[1.02] transition-transform duration-500">
                    </div>
                    <div>
                        <img src="{{ !empty($cms->heritage_image_3) ? Storage::url($cms->heritage_image_3) : asset('placeholder.png') }}"
                            class="rounded-xl shadow-lg aspect-[3/4] object-cover mb-4 border-2 border-[#c01c1c]/20 hover:scale-[1.02] transition-transform duration-500">
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2">
                <span class="inline-block text-[#c01c1c] font-bold tracking-[0.3em] uppercase text-xs mb-4">{{
                    $cms->heritage_subtitle ?? 'আমাদের রহস্য' }}</span>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black mb-6 leading-tight text-[#333333]">
                    {!! $cms->heritage_title ?? 'ঐতিহ্যবাহী <span class="text-[#c01c1c]">স্বাদ</span>' !!}
                </h1>
                <p class="text-[#666666] text-base leading-relaxed mb-8">
                    {{ $cms->heritage_description ?? 'আমাদের রেসিপি প্রজন্মের পর প্রজন্ম থেকে এসেছে। সেরা মশলা, সেরা
                    স্বাদ।' }}
                </p>
                <div class="flex flex-col gap-6">
                    <div class="flex gap-4 items-start group">
                        <span class="text-2xl text-[#c01c1c] font-bold">০১.</span>
                        <div>
                            <h4 class="text-lg font-bold mb-1 text-[#333333]">{{ $cms->secret_title ?? 'ঐতিহ্যবাহী মশলা'
                                }}</h4>
                            <p class="text-[#666666] text-sm">{{ $cms->secret_description ?? 'সরাসরি কৃষকের কাছ থেকে
                                সংগ্রহ করা হয়।' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="section-padding bg-[#fafafa]">
        <div class="container-wide">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <!-- Mission -->
                <div class="group">
                    <div class="relative mb-8 overflow-hidden rounded-xl aspect-[16/9] shadow-lg">
                        <img src="{{ !empty($cms->mission_image) ? Storage::url($cms->mission_image) : asset('placeholder.png') }}"
                            alt="Our Mission"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#c01c1c]/60 to-transparent"></div>
                        <div class="absolute bottom-6 left-6">
                            <span class="text-white font-bold tracking-[0.2em] uppercase text-xs block mb-1">{{
                                $cms->mission_subtitle ?? 'আমাদের লক্ষ্য' }}</span>
                            <h3 class="text-white text-2xl font-bold">{{ $cms->mission_title ?? 'আমাদের লক্ষ্য' }}</h3>
                        </div>
                    </div>
                    <p class="text-[#666666] text-base leading-relaxed border-l-4 border-[#c01c1c] pl-6 py-2">
                        "{{ $cms->mission_description ?? 'বাংলাদেশের ঐতিহ্যবাহী খাবার সংরক্ষণ ও প্রচার করা।' }}"
                    </p>
                </div>

                <!-- Vision -->
                <div class="group">
                    <div class="relative mb-8 overflow-hidden rounded-xl aspect-[16/9] shadow-lg">
                        <img src="{{ !empty($cms->vision_image) ? Storage::url($cms->vision_image) : asset('placeholder.png') }}"
                            alt="Our Vision"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#f97316]/60 to-transparent"></div>
                        <div class="absolute bottom-6 left-6">
                            <span class="text-white font-bold tracking-[0.2em] uppercase text-xs block mb-1">{{
                                $cms->vision_subtitle ?? 'আমাদের স্বপ্ন' }}</span>
                            <h3 class="text-white text-2xl font-bold">{{ $cms->vision_title ?? 'আমাদের স্বপ্ন' }}</h3>
                        </div>
                    </div>
                    <p class="text-[#666666] text-base leading-relaxed border-l-4 border-[#f97316] pl-6 py-2">
                        "{{ $cms->vision_description ?? 'বাংলাদেশের রন্ধনশিল্পের বৈশ্বিক দূত হওয়া।' }}"
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Signature Menu Section -->
    <section id="menu" class="section-padding bg-white">
        <div class="container-wide">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="inline-block text-[#c01c1c] font-bold tracking-[0.3em] uppercase text-xs mb-4">{{
                    $cms->menu_subtitle ?? 'জনপ্রিয় মেনু' }}</span>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black mb-4 text-[#333333]">{!! $cms->menu_title ??
                    'আমাদের <span class="text-[#c01c1c]">স্পেশাল</span>' !!}</h1>
                <p class="text-[#666666] text-base">{{ $cms->menu_description ?? 'বাংলাদেশের সেরা খাবারের সমাহার' }}</p>
            </div>

            <livewire:frontend.featured-menu />

            <div class="mt-12 text-center">
                <a href="/menu" wire:navigate
                    class="bg-[#c01c1c] text-white px-10 py-4 text-sm font-bold tracking-wider inline-flex items-center gap-3 rounded-lg hover:bg-[#d92e2e] transition-all">
                    <span>{{ $cms->menu_button_text ?? 'সব মেনু দেখুন' }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="section-padding bg-[#fafafa]">
        <div class="container-wide">
            <div class="flex flex-col md:flex-row justify-between items-end mb-10">
                <div class="max-w-xl">
                    <span class="inline-block text-[#c01c1c] font-bold tracking-[0.3em] uppercase text-xs mb-3">{{
                        $cms->visual_story_subtitle ?? 'গ্যালারি' }}</span>
                    <h1 class="text-3xl md:text-4xl font-black text-[#333333]">{!! $cms->visual_story_title ?? 'আমাদের
                        <span class="text-[#c01c1c]">পরিবেশ</span>' !!}</h1>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="col-span-2 row-span-2 rounded-xl overflow-hidden shadow-lg group">
                    <img src="{{ !empty($cms->visual_story_image_1) ? Storage::url($cms->visual_story_image_1) : asset('placeholder.png') }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                </div>
                <div class="rounded-xl overflow-hidden shadow-lg aspect-square group">
                    <img src="{{ !empty($cms->visual_story_image_2) ? Storage::url($cms->visual_story_image_2) : asset('placeholder.png') }}"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="rounded-xl overflow-hidden shadow-lg aspect-square group">
                    <img src="{{ !empty($cms->visual_story_image_3) ? Storage::url($cms->visual_story_image_3) : asset('placeholder.png') }}"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="col-span-2 rounded-xl overflow-hidden shadow-lg aspect-[2/1] group">
                    <img src="{{ !empty($cms->visual_story_image_4) ? Storage::url($cms->visual_story_image_4) : asset('placeholder.png') }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                </div>
            </div>
        </div>
    </section>

    <!-- Custom Blocks Section -->
    @php
    $customBlocks = [];
    if (! empty($cms->custom_blocks)) {
    $decodedCustomBlocks = json_decode($cms->custom_blocks, true);
    $customBlocks = is_array($decodedCustomBlocks) ? $decodedCustomBlocks : [];
    }
    @endphp
    @if(count($customBlocks) > 0)
    <section class="section-padding bg-white">
        <div class="container-wide space-y-8 lg:space-y-10">
            @foreach($customBlocks as $index => $block)
            <div class="grid lg:grid-cols-12 gap-8 lg:gap-10 items-center rounded-2xl border border-[#e5e5e5] p-6 lg:p-8 bg-[#fafafa]"
                wire:key="custom-block-{{ $index }}">
                <div class="{{ $index % 2 === 0 ? 'lg:col-span-5' : 'lg:col-span-7 lg:order-2' }}">
                    <div class="rounded-xl overflow-hidden border border-[#e5e5e5] bg-white">
                        <img src="{{ !empty($block['image']) ? Storage::url($block['image']) : asset('placeholder.png') }}"
                            alt="{{ $block['title'] ?? 'Custom Block Image' }}"
                            class="w-full h-[260px] md:h-[320px] object-cover">
                    </div>
                </div>

                <div class="{{ $index % 2 === 0 ? 'lg:col-span-7' : 'lg:col-span-5 lg:order-1' }}">
                    @if(!empty($block['subtitle']))
                    <span class="inline-block text-[#c01c1c] font-bold tracking-[0.3em] uppercase text-xs mb-4">{{
                        $block['subtitle'] }}</span>
                    @endif

                    @if(!empty($block['title']))
                    <h2 class="text-3xl md:text-4xl font-black text-[#333333] mb-4 leading-tight">{{ $block['title'] }}
                    </h2>
                    @endif

                    @if(!empty($block['description']))
                    <p class="text-[#666666] text-base leading-relaxed mb-6">{{ $block['description'] }}</p>
                    @endif

                    @if(!empty($block['button_text']) && !empty($block['button_link']))
                    <a href="{{ $block['button_link'] }}"
                        class="inline-flex items-center gap-2 bg-[#c01c1c] text-white px-7 py-3 rounded-lg text-sm font-bold tracking-wider hover:bg-[#d92e2e] transition-all">
                        <span>{{ $block['button_text'] }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Reservation Section -->
    <section id="reservation" class="section-padding bg-white">
        <div class="container-wide">
            <div class="grid lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-5">
                    <span class="inline-block text-[#c01c1c] font-bold tracking-[0.3em] uppercase text-xs mb-4">{{
                        $cms->reservation_subtitle ?? 'টেবিল বুকিং' }}</span>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-black mb-6 leading-none text-[#333333]">{!!
                        $cms->reservation_title ?? 'আপনার জন্য <span class="text-[#c01c1c]">টেবিল</span> রিজার্ভ' !!}
                    </h1>
                    <p class="text-[#666666] text-base mb-8 leading-relaxed">{{ $cms->reservation_description ??
                        'প্রাইভেট ইভেন্ট ও কর্পোরেট গাদারিং এর জন্য আমরা সর্বদা প্রস্তুত।' }}</p>

                    <!-- Contact Card -->
                    <div class="flex items-center gap-4 p-4 bg-[#fafafa] rounded-xl border border-[#e5e5e5]">
                        <div
                            class="w-12 h-12 bg-[#c01c1c]/10 rounded-full flex items-center justify-center text-[#c01c1c]">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-widest text-[#c01c1c] mb-1">{{
                                $cms->reservation_contact_label ?? 'হেল্পলাইন' }}</h4>
                            <p class="text-[#333333]">{{ App\Models\Setting::getValue('footer_phone', '+880 1234
                                567890') }}</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <div class="bg-[#fafafa] p-8 rounded-xl shadow-lg border border-[#e5e5e5]">
                        <livewire:frontend.reservation-form />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Customer Reviews Section -->
    <section class="section-padding bg-brand-bg-white relative overflow-hidden">
        {{-- Decorative background elements --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-primary/5 rounded-full blur-3xl -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-brand-gold/5 rounded-full blur-3xl -ml-32 -mb-32"></div>

        <div class="container-wide relative">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <div class="h-px w-8 bg-brand-primary"></div>
                    <span class="text-brand-primary font-bold tracking-[0.3em] uppercase text-xs">{{
                        $cms->reviews_subtitle ?? 'আমাদের গ্রাহকদের কথা' }}</span>
                    <div class="h-px w-8 bg-brand-primary"></div>
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-brand-text-dark mb-4">{!! $cms->reviews_title ??
                    'স্বাদের <span class="text-brand-primary">স্মৃতি</span>' !!}</h2>
                <p class="text-brand-text-gray italic text-lg">{{ $cms->reviews_description ?? 'আমাদের খাবারের স্বাদ
                    নিয়ে যারা মুগ্ধ, তাদের কিছু কথা।' }}</p>
            </div>

            <div class="reviews-slider -mx-4">
                @forelse(\App\Models\Review::where('is_active', true)->latest()->limit(10)->get() as $review)
                <div class="px-4 py-8">
                    <div
                        class="bg-white p-8 rounded-2xl shadow-xl border border-brand-primary/5 relative h-full group hover:-translate-y-2 transition-all duration-500">
                        {{-- Decorative Quote Icon --}}
                        <div
                            class="absolute top-6 right-8 text-brand-primary/10 group-hover:text-brand-primary/20 transition-colors">
                            <svg class="w-12 h-12 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017C19.5693 16 20.017 15.5523 20.017 15V9C20.017 8.44772 19.5693 8 19.017 8H15.017C14.4647 8 14.017 8.44772 14.017 9V12C14.017 12.5523 13.5693 13 13.017 13H11.017V21H14.017ZM5.017 21L5.017 18C5.017 16.8954 5.91243 16 7.017 16H10.017C10.5693 16 11.017 15.5523 11.017 15V9C11.017 8.44772 10.5693 8 10.017 8H6.017C5.46472 8 5.017 8.44772 5.017 9V12C5.017 12.5523 4.5693 13 4.017 13H2.017V21H5.017Z" />
                            </svg>
                        </div>

                        <div class="flex flex-col h-full">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="relative">
                                    <div
                                        class="absolute inset-0 bg-brand-gold rounded-full scale-110 blur-[2px] opacity-20">
                                    </div>
                                    <img src="{{ !empty($review->customer_image) ? Storage::url($review->customer_image) : asset('placeholder.png') }}"
                                        alt="{{ $review->customer_name }}"
                                        class="w-14 h-14 rounded-full object-cover border-2 border-brand-gold relative z-10 shadow-md">
                                </div>
                                <div>
                                    <h4 class="font-bold text-brand-text-dark text-lg leading-tight">{{
                                        $review->customer_name }}</h4>
                                    <div class="flex gap-0.5 mt-1">
                                        @for($i = 1; $i <= 5; $i++) <svg
                                            class="w-4 h-4 {{ $i <= $review->rating ? 'text-brand-gold' : 'text-gray-300' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            @endfor
                                    </div>
                                </div>
                            </div>

                            <blockquote class="text-brand-text-gray text-base leading-relaxed grow relative z-10">
                                "{{ $review->comment }}"
                            </blockquote>

                            <div class="mt-6 pt-6 border-t border-brand-primary/5 flex items-center justify-between">
                                <span
                                    class="text-[10px] font-bold uppercase tracking-widest text-brand-primary/40">Verified
                                    Review</span>
                                <div class="flex gap-1">
                                    <div class="w-1.5 h-1.5 rounded-full bg-brand-gold"></div>
                                    <div class="w-1.5 h-1.5 rounded-full bg-brand-gold/40"></div>
                                    <div class="w-1.5 h-1.5 rounded-full bg-brand-gold/10"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div
                    class="col-span-full text-center py-20 bg-white rounded-3xl border-2 border-dashed border-brand-primary/10">
                    <div
                        class="w-20 h-20 bg-brand-primary/5 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-brand-primary/30" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <p class="text-brand-text-gray font-medium">বর্তমানে কোনো রিভিউ নেই। শীঘ্রই যোগ করা হবে।</p>
                </div>
                @endforelse
            </div>

            <style>
                .reviews-slider .slick-dots {
                    bottom: -40px;
                }

                .reviews-slider .slick-dots li button:before {
                    font-size: 12px;
                    color: #c01c1c;
                    opacity: 0.2;
                }

                .reviews-slider .slick-dots li.slick-active button:before {
                    color: #fbbf24;
                    opacity: 1;
                }
            </style>

            <script>
                $(document).ready(function(){
                    $('.reviews-slider').slick({
                        dots: true,
                        infinite: true,
                        speed: 800,
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 5000,
                        arrows: false,
                        pauseOnHover: true,
                        cssEase: 'cubic-bezier(0.77, 0, 0.175, 1)',
                        responsive: [
                            {
                                breakpoint: 1150,
                                settings: {
                                    slidesToShow: 2,
                                }
                            },
                            {
                                breakpoint: 768,
                                settings: {
                                    slidesToShow: 1,
                                }
                            }
                        ]
                    });
                });
            </script>
        </div>
    </section>
    <!-- Delivery Partner Section (Online Order Blocks) -->
    @php($activePartners = \App\Models\DeliveryPartner::where('is_active', true)->get())
    @if($activePartners->count() > 0)
    <section class="section-padding bg-[#f4f7f9]">
        <div class="container-wide">
            <div class="max-w-5xl mx-auto text-center mb-10">
                <h2 class="text-4xl md:text-5xl font-black text-[#222222] mb-4 leading-tight">
                    অনলাইন অর্ডার প্লেস
                </h2>
                <p class="text-[#6b7280] text-base md:text-lg leading-relaxed max-w-2xl mx-auto">
                    একটি ক্লিকে প্রিয় প্ল্যাটফর্ম বেছে নিয়ে আমাদের থেকে অর্ডার করুন
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 md:gap-8 max-w-5xl mx-auto">
                @foreach($activePartners->take(3) as $partner)
                @if($partner->image)
                <a href="{{ $partner->phone ? 'tel:' . $partner->phone : '#' }}"
                    class="group relative h-52 md:h-56 rounded-2xl overflow-hidden bg-gradient-to-b from-[#f8fafc] via-[#bfc7d1] to-[#111827] shadow-lg border border-[#d1d5db] flex items-center justify-center px-8">
                    <img src="{{ Storage::url($partner->image) }}" alt="{{ $partner->name }}"
                        class="max-h-14 md:max-h-16 object-contain transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-black/70 to-transparent"></div>
                    <span
                        class="absolute bottom-4 left-6 text-white text-xl leading-none transition-transform duration-300 group-hover:translate-x-1">→</span>
                </a>
                @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif
</div>