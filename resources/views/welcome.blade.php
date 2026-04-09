<div class="homepage-wrapper">
    <x-slot:title>{{ $cms->hero_subtitle ?? 'Kacchi Bhai' }} - Authentic Bangladeshi Cuisine</x-slot:title>

    <!-- Hero Section - Kacchi Bhai Style (White/Red) -->
    <section class="relative min-h-[90vh] flex items-center bg-[#fafafa]">
        <div class="container-wide grid lg:grid-cols-12 gap-8 lg:gap-12 items-center py-20">
            <!-- Content - Left Side -->
            <div class="lg:col-span-6 order-2 lg:order-1 text-center lg:text-left">
                <div class="flex items-center justify-center lg:justify-start gap-3 mb-4">
                    <div class="h-px w-12 bg-[#c01c1c]"></div>
                    <span class="text-[#c01c1c] font-bold tracking-[0.3em] uppercase text-sm">{{ $cms->hero_subtitle ?? 'ঐতিহ্যবাহী স্বাদ' }}</span>
                    <div class="h-px w-12 bg-[#c01c1c]"></div>
                </div>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black mb-6 leading-tight text-[#333333]">
                    {!! $cms->hero_title ?? '<span class="text-[#c01c1c]">কাচ্চি</span> ভাই' !!}
                </h1>
                
                <p class="text-[#666666] text-lg md:text-xl mb-8 leading-relaxed max-w-xl mx-auto lg:mx-0">
                    {{ $cms->hero_description ?? 'আমাদের ঐতিহ্যবাহী কাচ্চি সর্বদা আপনার সেবায়। সেরা মশলা, সেরা স্বাদ।' }}
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="#menu" wire:navigate class="bg-[#c01c1c] text-white px-10 py-4 rounded-lg font-bold tracking-wide transition-all duration-300 hover:bg-[#d92e2e] hover:shadow-lg">
                        মেনু দেখুন
                    </a>
                    <a href="#reservation" wire:navigate class="border-2 border-[#c01c1c] text-[#c01c1c] px-10 py-4 rounded-lg font-bold tracking-wide transition-all duration-300 hover:bg-[#c01c1c] hover:text-white">
                        বুক করুন
                    </a>
                </div>

                <!-- Stats -->
                <div class="flex flex-wrap gap-8 mt-12 justify-center lg:justify-start">
                    <div class="text-center">
                        <span class="block text-3xl font-black text-[#c01c1c]">৫০+</span>
                        <span class="text-[#666666] text-sm">বছর অভিজ্ঞতা</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-3xl font-black text-[#c01c1c]">১০০+</span>
                        <span class="text-[#666666] text-sm">খাবার</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-3xl font-black text-[#c01c1c]">৫০০০+</span>
                        <span class="text-[#666666] text-sm">গ্রাহক</span>
                    </div>
                </div>
            </div>

            <!-- Image - Right Side -->
            <div class="lg:col-span-6 order-1 lg:order-2">
                <div class="relative">
                    <div class="rounded-2xl overflow-hidden shadow-2xl border-4 border-[#c01c1c]/20">
                        <img src="{{ !empty($cms->hero_image) ? Storage::url($cms->hero_image) : asset('placeholder.png') }}" alt="Kacchi Bhai Special" class="w-full h-[500px] lg:h-[600px] object-cover">
                    </div>
                    <!-- Decorative badge -->
                    <div class="absolute -bottom-4 -right-4 bg-[#c01c1c] text-white px-6 py-3 rounded-xl shadow-lg">
                        <span class="font-bold text-lg">৳ {{ number_format($cms->hero_price ?? 450) }}</span>
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
                        <img src="{{ !empty($cms->heritage_image_1) ? Storage::url($cms->heritage_image_1) : asset('placeholder.png') }}" class="rounded-xl shadow-lg aspect-square object-cover mb-4 border-2 border-[#c01c1c]/20 hover:scale-[1.02] transition-transform duration-500">
                        <img src="{{ !empty($cms->heritage_image_2) ? Storage::url($cms->heritage_image_2) : asset('placeholder.png') }}" class="rounded-xl shadow-lg aspect-square object-cover border-2 border-[#c01c1c]/20 hover:scale-[1.02] transition-transform duration-500">
                    </div>
                    <div>
                        <img src="{{ !empty($cms->heritage_image_3) ? Storage::url($cms->heritage_image_3) : asset('placeholder.png') }}" class="rounded-xl shadow-lg aspect-[3/4] object-cover mb-4 border-2 border-[#c01c1c]/20 hover:scale-[1.02] transition-transform duration-500">
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2">
                <span class="inline-block text-[#c01c1c] font-bold tracking-[0.3em] uppercase text-xs mb-4">{{ $cms->heritage_subtitle ?? 'আমাদের রহস্য' }}</span>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black mb-6 leading-tight text-[#333333]">
                    {!! $cms->heritage_title ?? 'ঐতিহ্যবাহী <span class="text-[#c01c1c]">স্বাদ</span>' !!}
                </h1>
                <p class="text-[#666666] text-base leading-relaxed mb-8">
                    {{ $cms->heritage_description ?? 'আমাদের রেসিপি প্রজন্মের পর প্রজন্ম থেকে এসেছে। সেরা মশলা, সেরা স্বাদ।' }}
                </p>
                <div class="flex flex-col gap-6">
                    <div class="flex gap-4 items-start group">
                        <span class="text-2xl text-[#c01c1c] font-bold">০১.</span>
                        <div>
                            <h4 class="text-lg font-bold mb-1 text-[#333333]">{{ $cms->secret_title ?? 'ঐতিহ্যবাহী মশলা' }}</h4>
                            <p class="text-[#666666] text-sm">{{ $cms->secret_description ?? 'সরাসরি কৃষকের কাছ থেকে সংগ্রহ করা হয়।' }}</p>
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
                            <span class="text-white font-bold tracking-[0.2em] uppercase text-xs block mb-1">{{ $cms->mission_subtitle ?? 'আমাদের লক্ষ্য' }}</span>
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
                            <span class="text-white font-bold tracking-[0.2em] uppercase text-xs block mb-1">{{ $cms->vision_subtitle ?? 'আমাদের স্বপ্ন' }}</span>
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
                <span class="inline-block text-[#c01c1c] font-bold tracking-[0.3em] uppercase text-xs mb-4">জনপ্রিয় মেনু</span>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black mb-4 text-[#333333]">আমাদের <span class="text-[#c01c1c]">স্পেশাল</span></h1>
                <p class="text-[#666666] text-base">বাংলাদেশের সেরা খাবারের সমাহার</p>
            </div>

            <livewire:frontend.featured-menu />

            <div class="mt-12 text-center">
                <a href="/menu" wire:navigate class="bg-[#c01c1c] text-white px-10 py-4 text-sm font-bold tracking-wider inline-flex items-center gap-3 rounded-lg hover:bg-[#d92e2e] transition-all">
                    <span>সব মেনু দেখুন</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
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
                    <span class="inline-block text-[#c01c1c] font-bold tracking-[0.3em] uppercase text-xs mb-3">{{ $cms->visual_story_subtitle ?? 'গ্যালারি' }}</span>
                    <h1 class="text-3xl md:text-4xl font-black text-[#333333]">{!! $cms->visual_story_title ?? 'আমাদের <span class="text-[#c01c1c]">পরিবেশ</span>' !!}</h1>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="col-span-2 row-span-2 rounded-xl overflow-hidden shadow-lg group">
                    <img src="{{ !empty($cms->visual_story_image_1) ? Storage::url($cms->visual_story_image_1) : asset('placeholder.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                </div>
                <div class="rounded-xl overflow-hidden shadow-lg aspect-square group">
                    <img src="{{ !empty($cms->visual_story_image_2) ? Storage::url($cms->visual_story_image_2) : asset('placeholder.png') }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="rounded-xl overflow-hidden shadow-lg aspect-square group">
                    <img src="{{ !empty($cms->visual_story_image_3) ? Storage::url($cms->visual_story_image_3) : asset('placeholder.png') }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="col-span-2 rounded-xl overflow-hidden shadow-lg aspect-[2/1] group">
                    <img src="{{ !empty($cms->visual_story_image_4) ? Storage::url($cms->visual_story_image_4) : asset('placeholder.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                </div>
            </div>
        </div>
    </section>

    <!-- Reservation Section -->
    <section id="reservation" class="section-padding bg-white">
        <div class="container-wide">
            <div class="grid lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-5">
                    <span class="inline-block text-[#c01c1c] font-bold tracking-[0.3em] uppercase text-xs mb-4">টেবিল বুকিং</span>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-black mb-6 leading-none text-[#333333]">আপনার জন্য <span class="text-[#c01c1c]">টেবিল</span> রিজার্ভ</h1>
                    <p class="text-[#666666] text-base mb-8 leading-relaxed">প্রাইভেট ইভেন্ট ও কর্পোরেট গাদারিং এর জন্য আমরা সর্বদা প্রস্তুত।</p>
                    
                    <!-- Contact Card -->
                    <div class="flex items-center gap-4 p-4 bg-[#fafafa] rounded-xl border border-[#e5e5e5]">
                        <div class="w-12 h-12 bg-[#c01c1c]/10 rounded-full flex items-center justify-center text-[#c01c1c]">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-widest text-[#c01c1c] mb-1">হেল্পলাইন</h4>
                            <p class="text-[#333333]">{{ App\Models\Setting::getValue('footer_phone', '+880 1234 567890') }}</p>
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
    <section class="section-padding bg-[#fafafa]">
        <div class="container-wide">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="inline-block text-[#c01c1c] font-bold tracking-[0.3em] uppercase text-xs mb-4">গ্রাহকদের মতামত</span>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black mb-4 text-[#333333]">আমাদের <span class="text-[#c01c1c]">গ্রাহকরা</span> বলেন</h1>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse(\App\Models\Review::where('is_active', true)->latest()->limit(6)->get() as $review)
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-[#e5e5e5]">
                        <div class="flex items-center gap-3 mb-4">
                            <img src="{{ !empty($review->customer_image) ? Storage::url($review->customer_image) : asset('placeholder.png') }}" 
                                 alt="{{ $review->customer_name }}" 
                                 class="w-12 h-12 rounded-full object-cover">
                            <div>
                                <h4 class="font-bold text-[#333333]">{{ $review->customer_name }}</h4>
                                <div class="flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="text-[#f97316] text-sm">{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <p class="text-[#666666] text-sm leading-relaxed">"{{ $review->comment }}"</p>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8 text-[#666666]">
                        <p>কোনো রিভিউ পাওয়া যায়নি।</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Delivery Partner Count Section -->
    @php($partnerCount = \App\Models\DeliveryPartner::where('is_active', true)->count())
    @if($partnerCount > 0)
    <section class="py-12 bg-[#c01c1c] text-white">
        <div class="container-wide">
            <div class="text-center">
                <h2 class="text-2xl md:text-3xl font-black mb-2">আমাদের ডেলিভারি পার্টনার</h2>
                <div class="text-5xl md:text-6xl font-black text-[#f97316]">{{ $partnerCount }}</div>
                <p class="text-white/70 mt-2">জন পার্টনার আপনার খাবার দিচ্ছে দ্রুত</p>
            </div>
        </div>
    </section>
    @endif
</div>