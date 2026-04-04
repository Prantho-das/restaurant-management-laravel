<div class="min-h-screen bg-slate-50/50 pb-32 font-sans selection:bg-orange-100 selection:text-orange-900" 
     x-data="{ 
        activeCategory: '',
        searchOpen: false,
        scrolled: false,
        init() {
            window.addEventListener('scroll', () => {
                this.scrolled = window.scrollY > 20;
                
                // Track active category based on scroll position
                const sections = document.querySelectorAll('section[id^=\'cat-\']');
                let current = '';
                sections.forEach(section => {
                    const top = section.offsetTop - 150;
                    if (window.scrollY >= top) {
                        current = section.id;
                    }
                });
                this.activeCategory = current;
            });
        }
     }">
    
    <!-- Header with Glassmorphism -->
    <header class="sticky top-0 z-50 transition-all duration-300" 
            :class="scrolled ? 'bg-white/80 backdrop-blur-xl border-b border-slate-200/60 shadow-sm py-3' : 'bg-transparent py-5'">
        <div class="max-w-2xl mx-auto px-5 flex items-center justify-between">
            <div class="flex flex-col">
                <h1 class="text-xl font-bold tracking-tight text-slate-900 leading-tight">{{ $site_name }}</h1>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                    </span>
                    <p class="text-[10px] font-bold text-orange-600 uppercase tracking-[0.2em]">{{ $table->name }}</p>
                </div>
            </div>
            
            <button @click="searchOpen = !searchOpen" 
                    class="h-11 w-11 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-600 hover:text-orange-600 hover:border-orange-100 hover:bg-orange-50 transition-all duration-300">
                <svg x-show="!searchOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <svg x-show="searchOpen" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Search Overlay -->
        <div x-show="searchOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             x-cloak
             class="px-5 mt-4 max-w-2xl mx-auto">
            <div class="relative group">
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Craving something specific?"
                       class="w-full h-14 pl-12 pr-4 bg-slate-100/50 border-none rounded-2xl text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-orange-500/20 transition-all text-base">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-orange-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Content -->
    <div class="px-5 pt-8 pb-4 max-w-2xl mx-auto">
        <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-900 p-8 shadow-2xl shadow-slate-200">
            <div class="absolute top-0 right-0 -mr-20 -mt-20 h-64 w-64 rounded-full bg-orange-500/20 blur-3xl text-white"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 h-64 w-64 rounded-full bg-orange-600/10 blur-3xl text-white"></div>
            
            <div class="relative z-10">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-[10px] font-bold text-orange-400 uppercase tracking-widest mb-4">
                    {{ $badge_text }}
                </span>
                <h2 class="text-3xl font-black text-white leading-[1.1]">
                    {!! $hero_title !!}
                </h2>
                <p class="mt-4 text-slate-400 text-sm leading-relaxed max-w-[240px]">
                    {{ $hero_subtitle }}
                </p>
            </div>
            
            <div class="absolute right-[-20px] bottom-[-20px] opacity-20">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="w-48 h-48 fill-orange-500">
                    <path d="M44.7,-76.4C58.3,-69.2,70.1,-58.5,78.2,-45.5C86.3,-32.5,90.7,-17.2,89.5,-2.2C88.2,12.8,81.4,27.5,72.2,40.1C63,52.7,51.4,63.2,38.1,70.6C24.8,78,9.8,82.4,-5.2,82.4C-20.3,82.5,-35.4,78.2,-48.7,70.6C61.9,62.9,-73.4,52,-79.8,38.6C-86.2,25.2,-87.5,9.4,-85.4,-5.4C-83.3,-20.1,-77.7,-33.7,-68.9,-44.8C-60,-55.9,-47.9,-64.5,-35.3,-72.1C-22.7,-79.7,-9.6,-86.3,3.7,-87.4C17,-88.5,31.2,-83.6,44.7,-76.4Z" transform="translate(100 100)" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Category Snap Navigation -->
    <nav class="sticky top-[84px] z-40 px-5 pt-4 pb-2 transition-colors duration-300"
         :class="scrolled ? 'bg-white/90 backdrop-blur-xl border-b border-slate-100 shadow-sm' : 'bg-transparent'">
        <div class="max-w-2xl mx-auto overflow-x-auto no-scrollbar whitespace-nowrap flex gap-2.5 pb-2">
            @foreach($categories as $category)
                <a href="#cat-{{ $category->id }}" 
                   @click.prevent="document.getElementById('cat-{{ $category->id }}').scrollIntoView({ behavior: 'smooth', block: 'start' })"
                   class="px-5 py-2.5 rounded-2xl text-sm font-bold tracking-tight transition-all duration-300 border"
                   :class="activeCategory === 'cat-{{ $category->id }}' 
                        ? 'bg-orange-600 border-orange-600 text-white shadow-lg shadow-orange-200 scale-105' 
                        : 'bg-white border-slate-200 text-slate-500 hover:border-orange-200 hover:text-orange-600 shadow-sm'">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </nav>

    <!-- Menu Grid -->
    <div class="px-5 max-w-2xl mx-auto mt-8 space-y-16">
        @forelse($categories as $category)
            @if($category->menuItems->count() > 0)
                <section id="cat-{{ $category->id }}" class="scroll-mt-40">
                    <div class="flex items-end justify-between mb-8 px-1">
                        <div>
                            <span class="text-[10px] font-black text-orange-500 uppercase tracking-widest block mb-1">Collection</span>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ $category->name }}</h3>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                            {{ $category->menuItems->count() }}
                        </div>
                    </div>
                    
                    <div class="grid gap-5">
                        @foreach($category->menuItems as $item)
                            <div class="group relative bg-white rounded-[2rem] p-4 shadow-sm border border-slate-200/60 hover:shadow-xl hover:shadow-slate-200/50 hover:-translate-y-1 transition-all duration-500 flex gap-5">
                                <!-- Image Container -->
                                <div class="relative h-28 w-28 flex-shrink-0">
                                    <div class="h-full w-full overflow-hidden rounded-[1.5rem] bg-slate-100 ring-4 ring-slate-50">
                                        <img src="{{ !empty($item->image) ? Storage::url($item->image) : asset('placeholder.png') }}" 
                                             alt="{{ $item->name }}" 
                                             class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-700">
                                    </div>
                                    @if($item->is_featured)
                                        <div class="absolute -top-2 -left-2 bg-orange-600 text-white text-[10px] font-black px-2 py-1 rounded-lg shadow-lg rotate-[-10deg] uppercase tracking-tighter">
                                            Must Try
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex-grow flex flex-col justify-between py-1 pr-1">
                                    <div>
                                        <h4 class="text-lg font-black text-slate-900 group-hover:text-orange-600 transition-colors tracking-tight">{{ $item->name }}</h4>
                                        <p class="text-xs text-slate-500 line-clamp-2 mt-1.5 leading-relaxed font-medium">{{ $item->description }}</p>
                                    </div>
                                    <div class="flex items-center justify-between mt-4">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Price</span>
                                            <span class="text-xl font-black text-slate-900 tracking-tight">
                                                <span class="text-orange-600 text-sm">৳</span>{{ number_format($item->final_price, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @empty
            <div class="py-20 text-center">
                <div class="h-20 w-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900">No items found</h3>
                <p class="text-slate-500 mt-2">Try searching for something else</p>
            </div>
        @endforelse
    </div>

    <!-- Floating Footer Credit -->
    <footer class="mt-20 py-10 text-center max-w-2xl mx-auto border-t border-slate-100 px-5">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em]">Exclusively at {{ $site_name }}</p>
        <p class="mt-4 text-[9px] font-medium text-slate-300">
            {{ $footer_text }}
        </p>
    </footer>
    
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    [x-cloak] { display: none !important; }
</style>
