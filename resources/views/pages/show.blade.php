<x-layout>
    <x-slot:title>{{ $page->title }} - {{ App\Models\Setting::getValue('site_title', 'Royal Dine') }}</x-slot:title>

    <div class="page-content pt-20 overflow-hidden">
        @foreach($page->content as $block)
            @php 
                $data = $block['data']; 
                $blockType = $block['type'];
                $isDark = in_array($blockType, ['hero']);
            @endphp

            @if($blockType === 'hero')
                <section class="relative min-h-[70vh] flex items-center overflow-hidden bg-brand-emerald">
                    <!-- Background Elements -->
                    <div class="absolute inset-0 z-0">
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
                        <div class="absolute inset-0 bg-gradient-to-b from-brand-emerald-dark/60 via-brand-emerald/40 to-brand-emerald-dark/80"></div>
                        <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-gold/20 rounded-full blur-[120px] animate-pulse"></div>
                        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-brand-primary/20 rounded-full blur-[120px] animate-pulse delay-1000"></div>
                    </div>

                    <div class="container-wide relative z-10 py-20">
                        <div class="grid lg:grid-cols-12 gap-16 items-center">
                            <div class="{{ ($data['alignment'] ?? 'center') === 'center' ? 'lg:col-span-12 text-center' : 'lg:col-span-7' }} animate-fade-in-up">
                                @if(!empty($data['subtitle']))
                                    <span class="inline-block px-4 py-1.5 mb-6 rounded-full bg-brand-gold/10 border border-brand-gold/20 text-brand-gold font-bold tracking-[0.2em] uppercase text-[10px] animate-fade-in-up delay-100">
                                        {{ $data['subtitle'] }}
                                    </span>
                                @endif
                                
                                <h1 class="font-serif text-5xl md:text-7xl lg:text-8xl text-white mb-8 leading-[1.1] animate-fade-in-up delay-150">
                                    {!! str_replace(['{', '}'], ['<span class="text-brand-gold">', '</span>'], e($data['title'])) !!}
                                </h1>

                                @if(!empty($data['description']))
                                    <div class="text-lg md:text-xl text-parchment/80 max-w-2xl {{ ($data['alignment'] ?? 'center') === 'center' ? 'mx-auto' : '' }} prose prose-invert mb-10 animate-fade-in-up delay-200">
                                        {!! $data['description'] !!}
                                    </div>
                                @endif

                                @if(!empty($data['button_text']) && !empty($data['button_link']))
                                    <div class="animate-fade-in-up delay-300">
                                        <a href="{{ $data['button_link'] }}" class="btn-royal text-lg px-10">
                                            {{ $data['button_text'] }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                           
                            @if(!empty($data['image']) && ($data['alignment'] ?? 'center') !== 'center')
                                <div class="lg:col-span-5 relative animate-fade-in-up delay-300">
                                    <div class="relative z-10 rounded-[2.5rem] overflow-hidden shadow-2xl aspect-[4/5] group">
                                        <div class="absolute inset-0 bg-brand-emerald/10 group-hover:bg-transparent transition-colors duration-500 z-10"></div>
                                        <img src="{{ Storage::url($data['image']) }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                                    </div>
                                    <!-- Decorative elements -->
                                    <div class="absolute -top-6 -right-6 w-32 h-32 border-2 border-brand-gold/30 rounded-3xl -z-0 animate-float"></div>
                                    <div class="absolute -bottom-6 -left-6 w-32 h-32 border-2 border-brand-primary/30 rounded-full -z-0 animate-float delay-750"></div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Scroll Down Prompt -->
                    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10 animate-bounce opacity-50">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </div>
                </section>

            @elseif($blockType === 'rich_text')
                <section class="section-padding bg-parchment relative">
                    <div class="container-wide">
                        <div class="max-w-4xl mx-auto bg-white p-12 md:p-20 rounded-[3rem] shadow-premium border border-brand-emerald/5 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-gold/5 rounded-full -mr-16 -mt-16"></div>
                            <div class="prose prose-lg md:prose-xl mx-auto prose-brand {{ $data['width'] === 'narrow' ? 'max-w-2xl' : ($data['width'] === 'wide' ? 'max-w-none' : 'max-w-3xl') }} relative z-10">
                                {!! $data['content'] !!}
                            </div>
                        </div>
                    </div>
                </section>

            @elseif($blockType === 'image_text')
                <section class="section-padding bg-white overflow-hidden">
                    <div class="container-wide">
                        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">
                            <div class="{{ $data['image_position'] === 'right' ? 'lg:order-1' : 'lg:order-2' }} animate-on-scroll">
                                @if(!empty($data['title']))
                                    <h2 class="font-serif text-4xl md:text-5xl text-brand-emerald mb-8 leading-tight">
                                        {{ $data['title'] }}
                                        <div class="h-1.5 w-20 bg-brand-gold mt-4 rounded-full"></div>
                                    </h2>
                                @endif
                                <div class="prose prose-brand text-lg text-slate-600">
                                    {!! $data['content'] !!}
                                </div>
                                @if(!empty($data['button_text']) && !empty($data['button_link']))
                                    <div class="mt-10">
                                        <a href="{{ $data['button_link'] }}" class="inline-flex items-center text-brand-emerald font-bold group">
                                            <span class="border-b-2 border-brand-gold group-hover:border-brand-emerald transition-colors pb-1 mr-2">{{ $data['button_text'] }}</span>
                                            <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="{{ $data['image_position'] === 'right' ? 'lg:order-2' : 'lg:order-1' }} relative">
                                <div class="rounded-[2.5rem] overflow-hidden shadow-luxury aspect-[4/3] group relative">
                                    <img src="{{ Storage::url($data['image']) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                    <div class="absolute inset-0 ring-1 ring-inset ring-black/10 rounded-[2.5rem]"></div>
                                </div>
                                <!-- Aesthetic accent -->
                                <div class="absolute -z-10 {{ $data['image_position'] === 'right' ? '-right-10' : '-left-10' }} -bottom-10 w-40 h-40 bg-brand-primary/5 rounded-full blur-2xl"></div>
                            </div>
                        </div>
                    </div>
                </section>

            @elseif($blockType === 'featured_menu')
                <section class="section-padding bg-brand-emerald-dark text-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-subtle-pattern opacity-5"></div>
                    <div class="container-wide relative z-10">
                        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
                            <div>
                                @if(!empty($data['subtitle']))
                                    <p class="text-brand-gold font-bold tracking-[0.3em] uppercase text-[10px] mb-4">{{ $data['subtitle'] }}</p>
                                @endif
                                <h2 class="font-serif text-4xl md:text-5xl text-white">{{ $data['title'] }}</h2>
                            </div>
                            <div class="hidden md:block">
                                <div class="h-px w-32 bg-brand-gold/30 mb-4"></div>
                            </div>
                        </div>
                        
                        <div class="bg-white/5 backdrop-blur-md rounded-[3rem] p-8 md:p-12 border border-white/10 shadow-2xl">
                             <livewire:frontend.featured-menu />
                        </div>
                    </div>
                </section>

            @elseif($blockType === 'faq')
                <section class="section-padding bg-parchment">
                    <div class="container-wide max-w-4xl">
                        <div class="text-center mb-16">
                            <h2 class="font-serif text-4xl md:text-5xl text-brand-emerald mb-4">{{ $data['title'] }}</h2>
                            <div class="w-24 h-1 bg-brand-gold mx-auto rounded-full"></div>
                        </div>
                        
                        <div class="space-y-4" x-data="{ active: null }">
                            @foreach($data['items'] as $idx => $item)
                                <div class="bg-white rounded-[2rem] shadow-sm border border-brand-emerald/5 overflow-hidden transition-all duration-300 hover:shadow-md"
                                     :class="active === {{ $idx }} ? 'ring-2 ring-brand-gold/20 translate-y-[-2px]' : ''">
                                    <button @click="active = (active === {{ $idx }} ? null : {{ $idx }})" 
                                            class="w-full px-8 py-6 text-left flex justify-between items-center group transition-colors">
                                        <span class="font-bold text-lg text-brand-emerald group-hover:text-brand-gold transition-colors pr-8">{{ $item['question'] }}</span>
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-brand-primary/5 flex items-center justify-center group-hover:bg-brand-gold group-hover:text-white transition-all duration-300"
                                             :class="active === {{ $idx }} ? 'bg-brand-gold text-white rotate-180' : 'text-brand-emerald'">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </button>
                                    <div x-show="active === {{ $idx }}" 
                                         x-collapse 
                                         class="px-8 pb-8 text-slate-600 text-lg leading-relaxed pt-4 border-t border-slate-50">
                                        <div class="bg-parchment/50 p-6 rounded-2xl">
                                            {{ $item['answer'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif
        @endforeach
    </div>
</x-layout>

