<x-layout>
    <x-slot:title>{{ $page->title }} - {{ App\Models\Setting::getValue('site_title', 'Royal Dine') }}</x-slot:title>

    <div class="page-content overflow-hidden">
        @foreach($page->content as $block)
            @php 
                $data = $block['data']; 
                $blockType = $block['type'];
            @endphp

            @if($blockType === 'hero')
                <section class="relative min-h-[85vh] flex items-center overflow-hidden bg-gradient-to-br from-brand-emerald via-brand-emerald-dark to-brand-emerald">
                    <!-- Animated Background -->
                    <div class="absolute inset-0 z-0">
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5 mix-blend-overlay"></div>
                        <div class="absolute inset-0 bg-gradient-to-b from-brand-emerald-dark/50 via-transparent to-brand-emerald-dark/70"></div>
                        <!-- Floating orbs -->
                        <div class="absolute top-1/4 left-1/4 w-72 h-72 bg-brand-gold/10 rounded-full blur-[100px] animate-pulse"></div>
                        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-brand-primary/10 rounded-full blur-[120px] animate-pulse delay-700"></div>
                        <div class="absolute top-1/2 right-1/3 w-48 h-48 bg-brand-gold/5 rounded-full blur-[80px] animate-float"></div>
                    </div>

                    <!-- Decorative lines -->
                    <div class="absolute inset-0 z-0 opacity-20">
                        <div class="absolute top-20 left-10 w-px h-32 bg-gradient-to-b from-transparent via-brand-gold to-transparent"></div>
                        <div class="absolute bottom-32 right-20 w-px h-48 bg-gradient-to-b from-transparent via-brand-primary to-transparent"></div>
                    </div>

                    <div class="container-wide relative z-10 py-20">
                        <div class="grid lg:grid-cols-12 gap-12 items-center">
                            <div class="{{ ($data['alignment'] ?? 'center') === 'center' ? 'lg:col-span-12 text-center' : 'lg:col-span-6' }} animate-fade-in-up">
                                @if(!empty($data['subtitle']))
                                    <span class="inline-flex items-center gap-2 px-4 py-2 mb-8 rounded-full bg-white/10 border border-white/20 text-[#c01c1c] font-medium tracking-[0.25em] uppercase text-xs animate-fade-in-up delay-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-brand-gold animate-pulse"></span>
                                        {{ $data['subtitle'] }}
                                    </span>
                                @endif
                                
                                <h1 class="font-serif text-6xl md:text-7xl lg:text-8xl text-[#c01c1c] mb-8 leading-[1.05] animate-fade-in-up delay-150">
                                    {!! str_replace(['{', '}'], ['<span class="text-brand-gold relative">', '</span>'], e($data['title'])) !!}
                                    <span class="absolute -bottom-2 left-0 w-full h-1 bg-brand-gold/50 rounded-full"></span>
                                </h1>

                                @if(!empty($data['description']))
                                    <div class="text-xl md:text-2xl text-[#c01c1c]/80 max-w-2xl {{ ($data['alignment'] ?? 'center') === 'center' ? 'mx-auto' : '' }} prose prose-invert mb-12 animate-fade-in-up delay-200">
                                        {!! $data['description'] !!}
                                    </div>
                                @endif

                                @if(!empty($data['button_text']) && !empty($data['button_url']))
                                    <div class="flex flex-wrap gap-4 animate-fade-in-up delay-300 {{ ($data['alignment'] ?? 'center') === 'center' ? 'justify-center' : '' }}">
                                        <a href="{{ $data['button_url'] }}" class="group relative px-10 py-4 bg-brand-gold text-brand-emerald-dark font-bold text-lg rounded-full overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-xl">
                                            <span class="absolute inset-0 w-full h-full bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></span>
                                            <span class="relative flex items-center gap-2">
                                                {{ $data['button_text'] }}
                                                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                </svg>
                                            </span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                           
                            @if(!empty($data['image']) && ($data['alignment'] ?? 'center') !== 'center')
                                <div class="lg:col-span-6 relative animate-fade-in-up delay-400">
                                    <div class="relative z-10">
                                        <div class="relative rounded-[3rem] overflow-hidden shadow-2xl aspect-[4/5] group">
                                            <div class="absolute inset-0 bg-gradient-to-t from-brand-emerald-dark/50 via-transparent to-transparent z-10"></div>
                                            <img src="{{ Storage::url($data['image']) }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" alt="{{ $data['title'] }}">
                                        </div>
                                    </div>
                                    <!-- Floating card -->
                                    <div class="absolute -bottom-8 -left-8 bg-white/10 backdrop-blur-md px-6 py-4 rounded-2xl border border-white/20 animate-float">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-brand-gold flex items-center justify-center">
                                                <svg class="w-5 h-5 text-brand-emerald-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12c0 5.18-3.95 9.45-10 11.5-6.056-2.05-10-6.33-10-11.5l2.143-4.143L13 3" />
                                                </svg>
                                            </div>
                                            <div class="text-[#c01c1c]">
                                                <p class="font-bold">Premium Dining</p>
                                                <p class="text-xs text-[#c01c1c]/60">Experience luxury</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Scroll Indicator -->
                    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10">
                        <div class="flex flex-col items-center gap-2 text-[#c01c1c]/50 animate-bounce">
                            <span class="text-xs tracking-widest uppercase">Scroll</span>
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </div>
                    </div>
                </section>

            @elseif($blockType === 'rich_text')
                <section class="section-padding bg-gradient-to-b from-parchment to-white relative">
                    <div class="container-wide">
                        <div class="max-w-4xl mx-auto">
                            <div class="relative bg-white rounded-[3rem] shadow-premium border border-brand-emerald/5 p-12 md:p-16 overflow-hidden">
                                <!-- Decorative -->
                                <div class="absolute top-0 right-0 w-40 h-40 bg-brand-gold/5 rounded-full -mr-20 -mt-20"></div>
                                <div class="absolute bottom-0 left-0 w-32 h-32 bg-brand-emerald/5 rounded-full -ml-16 -mb-16"></div>
                                
                                <div class="prose prose-lg md:prose-xl mx-auto prose-brand relative z-10 {{ $data['width'] === 'narrow' ? 'max-w-2xl' : ($data['width'] === 'wide' ? 'max-w-none' : 'max-w-3xl') }}">
                                    {!! $data['content'] !!}
                                </div>
                                
                                @if(!empty($data['button_text']) && !empty($data['button_url']))
                                    <div class="mt-12 text-center relative z-10">
                                        <a href="{{ $data['button_url'] }}" class="inline-flex items-center gap-2 px-8 py-3 bg-brand-emerald text-[#c01c1c] font-bold rounded-full hover:bg-brand-emerald-dark transition-colors duration-300">
                                            {{ $data['button_text'] }}
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>

            @elseif($blockType === 'image_text')
                <section class="section-padding bg-white relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-parchment to-transparent"></div>
                    <div class="container-wide">
                        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">
                            <div class="relative {{ $data['image_position'] === 'right' ? 'lg:order-1' : 'lg:order-2' }} animate-on-scroll">
                                <div class="relative rounded-[3rem] overflow-hidden shadow-luxury aspect-[4/3] group">
                                    <div class="absolute inset-0 bg-gradient-to-t from-brand-emerald/20 via-transparent to-transparent z-10"></div>
                                    <img src="{{ Storage::url($data['image']) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="{{ $data['title'] }}">
                                    <div class="absolute inset-0 ring-1 ring-inset ring-black/5 rounded-[3rem]"></div>
                                </div>
                                <!-- Floating badge -->
                                <div class="absolute -bottom-6 -right-6 bg-brand-emerald text-[#c01c1c] px-6 py-3 rounded-2xl shadow-lg animate-float">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-brand-gold" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                        <span class="font-bold">5.0</span>
                                        <span class="text-[#c01c1c]/70 text-sm">Rating</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="{{ $data['image_position'] === 'right' ? 'lg:order-2' : 'lg:order-1' }} animate-on-scroll">
                                @if(!empty($data['title']))
                                    <h2 class="font-serif text-4xl md:text-5xl text-brand-emerald mb-6 leading-tight">
                                        {{ $data['title'] }}
                                    </h2>
                                    <div class="h-1.5 w-24 bg-brand-gold mb-8 rounded-full"></div>
                                @endif
                                <div class="prose prose-brand text-lg text-slate-600 mb-8">
                                    {!! $data['content'] !!}
                                </div>
                                @if(!empty($data['button_text']) && !empty($data['button_url']))
                                    <div>
                                        <a href="{{ $data['button_url'] }}" class="group inline-flex items-center gap-3 text-brand-emerald font-bold text-lg">
                                            <span class="border-b-2 border-brand-emerald group-hover:border-brand-gold group-hover:text-brand-gold transition-colors pb-1">{{ $data['button_text'] }}</span>
                                            <svg class="w-6 h-6 transition-transform group-hover:translate-x-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-white to-transparent"></div>
                </section>

            @elseif($blockType === 'featured_menu')
                <section class="section-padding bg-gradient-to-b from-brand-emerald-dark to-brand-emerald relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-0 left-0 w-96 h-96 bg-brand-gold rounded-full blur-[150px]"></div>
                        <div class="absolute bottom-0 right-0 w-96 h-96 bg-brand-primary rounded-full blur-[150px]"></div>
                    </div>
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5"></div>
                    
                    <div class="container-wide relative z-10">
                        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
                            <div>
                                @if(!empty($data['subtitle']))
                                    <p class="inline-flex items-center gap-2 text-brand-gold font-bold tracking-[0.3em] uppercase text-xs mb-4">
                                        <span class="w-2 h-2 rounded-full bg-brand-gold"></span>
                                        {{ $data['subtitle'] }}
                                    </p>
                                @endif
                                <h2 class="font-serif text-4xl md:text-5xl text-[#c01c1c]">{{ $data['title'] }}</h2>
                            </div>
                            @if(!empty($data['button_text']) && !empty($data['button_url']))
                                <div>
                                    <a href="{{ $data['button_url'] }}" class="inline-flex items-center gap-2 px-8 py-3 bg-brand-gold text-brand-emerald-dark font-bold rounded-full hover:shadow-lg hover:scale-105 transition-all duration-300">
                                        {{ $data['button_text'] }}
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        <div class="bg-white/10 backdrop-blur-lg rounded-[3rem] p-8 md:p-12 border border-white/10 shadow-2xl">
                             <livewire:frontend.featured-menu />
                        </div>
                    </div>
                </section>

            @elseif($blockType === 'image_cards_section')
                <section class="section-padding bg-gradient-to-b from-parchment to-white relative">
                    <div class="container-wide max-w-6xl">
                        <div class="text-center mb-12 md:mb-16">
                            @if(!empty($data['subtitle']))
                                <span class="inline-flex items-center gap-2 px-4 py-2 mb-4 rounded-full bg-brand-emerald/10 text-brand-emerald font-bold tracking-[0.2em] uppercase text-xs">
                                    <span class="w-2 h-2 rounded-full bg-brand-emerald"></span>
                                    {{ $data['subtitle'] }}
                                </span>
                            @endif

                            @if(!empty($data['title']))
                                <h2 class="font-serif text-4xl md:text-5xl text-brand-emerald mb-4">{{ $data['title'] }}</h2>
                                <div class="w-24 h-1.5 bg-brand-gold mx-auto rounded-full mb-5"></div>
                            @endif

                            @if(!empty($data['description']))
                                <p class="text-slate-600 text-lg max-w-2xl mx-auto">{{ $data['description'] }}</p>
                            @endif
                        </div>

                        @php
                            $cards = collect($data['cards'] ?? [])->filter(fn ($card) => ! empty($card['image']));
                        @endphp

                        @if($cards->isNotEmpty())
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 md:gap-8">
                                @foreach($cards as $card)
                                    @php
                                        $cardUrl = ! empty($card['link']) ? $card['link'] : null;
                                        $cardAlt = $card['alt'] ?? ($data['title'] ?? 'Ordering card');
                                    @endphp

                                    @if($cardUrl)
                                        <a href="{{ $cardUrl }}" class="group relative block rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden hover:shadow-xl transition-shadow duration-300" target="_blank" rel="noopener noreferrer">
                                    @else
                                        <div class="group relative rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                                    @endif
                                            <div class="relative aspect-[4/3] p-8 md:p-10 bg-gradient-to-b from-slate-100 to-slate-800">
                                                <img src="{{ Storage::url($card['image']) }}" alt="{{ $cardAlt }}" class="w-full h-full object-contain drop-shadow-xl">
                                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 via-transparent to-transparent"></div>

                                                <div class="absolute right-5 bottom-5 w-11 h-11 rounded-full bg-white/90 text-slate-800 flex items-center justify-center shadow-md transition-transform duration-300 group-hover:translate-x-1 group-hover:-translate-y-1">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </div>
                                            </div>
                                    @if($cardUrl)
                                        </a>
                                    @else
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="max-w-xl mx-auto rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-slate-500">
                                কোনও কার্ড এখনো যোগ করা হয়নি।
                            </div>
                        @endif
                    </div>
                </section>

            @elseif($blockType === 'image_cards_section_modern')
                <section class="section-padding relative overflow-hidden bg-slate-950">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(16,185,129,0.2),_transparent_45%),radial-gradient(circle_at_bottom_left,_rgba(245,158,11,0.16),_transparent_40%)]"></div>
                    <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cartographer.png')]"></div>

                    <div class="container-wide max-w-6xl relative z-10">
                        <div class="text-center mb-12 md:mb-16">
                            @if(!empty($data['subtitle']))
                                <span class="inline-flex items-center gap-2 px-4 py-2 mb-4 rounded-full border border-emerald-300/30 bg-emerald-400/10 text-emerald-200 font-semibold tracking-[0.2em] uppercase text-xs">
                                    <span class="w-2 h-2 rounded-full bg-emerald-300"></span>
                                    {{ $data['subtitle'] }}
                                </span>
                            @endif

                            <h2 class="font-serif text-4xl md:text-5xl text-white mb-4">{{ $data['title'] ?? 'Order Online' }}</h2>

                            @if(!empty($data['description']))
                                <p class="text-slate-300 text-lg max-w-2xl mx-auto">{{ $data['description'] }}</p>
                            @endif
                        </div>

                        @php
                            $cards = collect($data['cards'] ?? [])->filter(fn ($card) => ! empty($card['image']));
                        @endphp

                        @if($cards->isNotEmpty())
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 md:gap-8">
                                @foreach($cards as $card)
                                    @php
                                        $cardUrl = ! empty($card['link']) ? $card['link'] : null;
                                        $cardAlt = $card['alt'] ?? ($data['title'] ?? 'Ordering card');
                                    @endphp

                                    @if($cardUrl)
                                        <a href="{{ $cardUrl }}" class="group block" target="_blank" rel="noopener noreferrer">
                                    @else
                                        <div class="group block">
                                    @endif
                                            <article class="relative h-full rounded-[2rem] p-[1px] bg-gradient-to-br from-emerald-300/70 via-slate-400/20 to-amber-300/60 shadow-[0_20px_60px_-25px_rgba(15,23,42,0.8)]">
                                                <div class="relative h-full rounded-[calc(2rem-1px)] bg-slate-900/90 backdrop-blur overflow-hidden">
                                                    <div class="absolute inset-x-0 top-0 h-1/2 bg-gradient-to-b from-white/10 to-transparent"></div>
                                                    <div class="relative aspect-[4/3] p-8 md:p-10 flex items-center justify-center">
                                                        <img src="{{ Storage::url($card['image']) }}" alt="{{ $cardAlt }}" class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105">
                                                    </div>
                                                    <div class="absolute inset-x-0 bottom-0 p-5 flex items-center justify-between bg-gradient-to-t from-slate-950 via-slate-950/80 to-transparent">
                                                        <span class="text-slate-200 text-sm">Tap to open</span>
                                                        <span class="w-10 h-10 rounded-full border border-white/20 bg-white/10 text-white flex items-center justify-center transition-transform duration-300 group-hover:translate-x-1">
                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </div>
                                            </article>
                                    @if($cardUrl)
                                        </a>
                                    @else
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="max-w-xl mx-auto rounded-2xl border border-dashed border-slate-600 bg-slate-900/60 px-6 py-10 text-center text-slate-300">
                                কোনও কার্ড এখনো যোগ করা হয়নি।
                            </div>
                        @endif
                    </div>
                </section>

            @elseif($blockType === 'faq')
                <section class="section-padding bg-gradient-to-b from-parchment to-white relative">
                    <div class="container-wide max-w-4xl">
                        <div class="text-center mb-16">
                            <span class="inline-flex items-center gap-2 px-4 py-2 mb-4 rounded-full bg-brand-emerald/10 text-brand-emerald font-bold tracking-[0.2em] uppercase text-xs">
                                <span class="w-2 h-2 rounded-full bg-brand-emerald"></span>
                                Support
                            </span>
                            <h2 class="font-serif text-4xl md:text-5xl text-brand-emerald mb-4">{{ $data['title'] }}</h2>
                            <div class="w-24 h-1.5 bg-brand-gold mx-auto rounded-full"></div>
                        </div>
                        
                        <div class="space-y-4" x-data="{ active: null }">
                            @foreach($data['items'] as $idx => $item)
                                <div class="bg-white rounded-[2rem] shadow-sm border border-brand-emerald/5 overflow-hidden transition-all duration-300 hover:shadow-md"
                                     :class="active === {{ $idx }} ? 'ring-2 ring-brand-gold/20' : ''">
                                    <button @click="active = (active === {{ $idx }} ? null : {{ $idx }})" 
                                            class="w-full px-8 py-6 text-left flex justify-between items-center group transition-colors">
                                        <span class="font-bold text-lg text-brand-emerald group-hover:text-brand-gold transition-colors pr-8">{{ $item['question'] }}</span>
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-brand-emerald/5 flex items-center justify-center group-hover:bg-brand-gold group-hover:text-[#c01c1c] transition-all duration-300"
                                             :class="active === {{ $idx }} ? 'bg-brand-gold text-[#c01c1c] rotate-180' : 'text-brand-emerald'">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </button>
                                    <div x-show="active === {{ $idx }}" 
                                         x-collapse 
                                         class="px-8 pb-8 text-slate-600 text-lg leading-relaxed pt-2 border-t border-slate-100">
                                        <div class="bg-parchment/50 p-6 rounded-2xl">
                                            {{ $item['answer'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if(!empty($data['button_text']) && !empty($data['button_url']))
                            <div class="mt-12 text-center">
                                <a href="{{ $data['button_url'] }}" class="inline-flex items-center gap-2 px-8 py-4 bg-brand-emerald text-[#c01c1c] font-bold rounded-full hover:bg-brand-emerald-dark transition-colors duration-300">
                                    {{ $data['button_text'] }}
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </section>
            @endif
        @endforeach
    </div>
</x-layout>
