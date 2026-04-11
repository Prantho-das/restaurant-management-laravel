<div class="relative">
    {{-- Search + Category Navigation Row --}}
    <div class="mb-10 flex flex-col gap-6">
        {{-- Search bar --}}
        <div class="relative max-w-md mx-auto w-full">
            <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-[#666666]">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input
                wire:model.live.debounce.350ms="search"
                type="text"
                placeholder="খাবার খুঁজুন..."
                id="menu-search"
                class="w-full pl-12 pr-4 py-3 rounded-xl border border-[#e5e5e5] bg-white text-sm text-[#333333] placeholder-[#999999] shadow-sm focus:outline-none focus:ring-2 focus:ring-[#c01c1c]/20 focus:border-[#c01c1c]/30 transition-all duration-300"
            >
            @if($search)
                <button wire:click="$set('search', '')" class="absolute inset-y-0 right-4 flex items-center text-[#666666] hover:text-[#c01c1c] transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            @endif
        </div>

        {{-- Category Tabs --}}
        <div
            x-data="{
                canScrollLeft: false,
                canScrollRight: false,
                checkScroll() {
                    const el = this.$refs.scroller;
                    this.canScrollLeft = el.scrollLeft > 4;
                    this.canScrollRight = el.scrollLeft + el.clientWidth < el.scrollWidth - 4;
                },
                scrollBy(px) {
                    this.$refs.scroller.scrollBy({ left: px, behavior: 'smooth' });
                }
            }"
            x-init="$nextTick(() => checkScroll())"
            class="relative flex items-center gap-2"
        >
            {{-- Prev Arrow --}}
            <button
                @click="scrollBy(-200)"
                x-show="canScrollLeft"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90"
                class="hidden lg:flex flex-shrink-0 w-9 h-9 items-center justify-center rounded-full bg-white border border-[#e5e5e5] text-[#c01c1c] shadow-sm hover:bg-[#c01c1c] hover:text-white hover:border-[#c01c1c] hover:shadow-md transition-all duration-200 z-10"
                aria-label="Scroll categories left"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </button>

            {{-- Scrollable tabs --}}
            <div
                x-ref="scroller"
                @scroll="checkScroll()"
                class="overflow-x-auto no-scrollbar flex items-center gap-2 flex-1"
            >
                <div class="flex items-center gap-2 min-w-max">
                    <button
                        wire:click="selectCategory(null)"
                        class="px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all duration-300 {{ is_null($selectedCategoryId) ? 'bg-[#c01c1c] text-white shadow-lg shadow-[#c01c1c]/20' : 'bg-white text-[#666666] border border-[#e5e5e5] hover:border-[#c01c1c]/30 hover:text-[#c01c1c] hover:shadow-md' }}">
                        সব
                    </button>
                    @foreach($categories as $category)
                        <button
                            wire:click="selectCategory({{ $category->id }})"
                            class="px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all duration-300 {{ $selectedCategoryId == $category->id ? 'bg-[#c01c1c] text-white shadow-lg shadow-[#c01c1c]/20' : 'bg-white text-[#666666] border border-[#e5e5e5] hover:border-[#c01c1c]/30 hover:text-[#c01c1c] hover:shadow-md' }}">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Next Arrow --}}
            <button
                @click="scrollBy(200)"
                x-show="canScrollRight"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90"
                class="hidden lg:flex flex-shrink-0 w-9 h-9 items-center justify-center rounded-full bg-white border border-[#e5e5e5] text-[#c01c1c] shadow-sm hover:bg-[#c01c1c] hover:text-white hover:border-[#c01c1c] hover:shadow-md transition-all duration-200 z-10"
                aria-label="Scroll categories right"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    {{-- Result count --}}
    @if($totalCount > 0)
        <p class="text-xs text-[#666666]/50 font-semibold uppercase tracking-widest mb-8 text-center">
            {{ $menuItems->count() }} টি আইটেম দেখাচ্ছে (মোট {{ $totalCount }})
        </p>
    @endif

    {{-- Menu Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($menuItems as $index => $item)
            <div class="group relative bg-white rounded-2xl overflow-hidden shadow-lg border border-[#e5e5e5] hover:shadow-xl hover:border-[#c01c1c]/20 transition-all duration-300 flex flex-col sm:flex-row" wire:key="item-{{ $item->id }}">
                {{-- Image --}}
                <div class="sm:w-2/5 relative aspect-square sm:aspect-auto overflow-hidden">
                    <img src="{{ $item->image ? Storage::url($item->image) : asset('placeholder.png') }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                    @if($item->discount_price && $item->discount_price < $item->base_price)
                        <span class="absolute top-2 left-2 bg-[#c01c1c] text-white text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full shadow-lg">
                            -{{ round((($item->base_price - $item->discount_price) / $item->base_price) * 100) }}%
                        </span>
                    @endif
                </div>

                {{-- Content --}}
                <div class="sm:w-3/5 p-4 flex flex-col justify-center">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-bold text-[#333333]">{{ $item->name }}</h3>
                    </div>
                    <p class="text-[#666666] text-sm leading-relaxed mb-4 line-clamp-2">{{ $item->description }}</p>

                    <div class="flex items-center justify-between">
                        <div>
                            @if($item->discount_price && $item->discount_price < $item->base_price)
                                <span class="text-lg font-black text-[#c01c1c]">৳{{ number_format($item->final_price, 0) }}</span>
                                <span class="text-sm text-[#999999] line-through ml-2">৳{{ number_format($item->base_price, 0) }}</span>
                            @else
                                <span class="text-lg font-black text-[#c01c1c]">৳{{ number_format($item->final_price, 0) }}</span>
                            @endif
                        </div>
                        <button wire:click="addToCart({{ $item->id }})"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-70 cursor-wait"
                            class="flex items-center gap-2 bg-[#c01c1c] text-white text-xs font-bold px-4 py-2 rounded-lg transition-all duration-300 hover:bg-[#d92e2e] active:scale-95">
                            <svg wire:loading.remove wire:target="addToCart({{ $item->id }})" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            <svg wire:loading wire:target="addToCart({{ $item->id }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span>কার্ট</span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-[#c01c1c]/5 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-[#c01c1c]/30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <h3 class="text-lg font-bold text-[#333333] mb-2">কোনো আইটেম পাওয়া যায়নি</h3>
                <p class="text-sm text-[#666666]">অন্য ক্যাটাগরি বা সার্চ টার্ম চেষ্টা করুন।</p>
            </div>
        @endforelse
    </div>

    {{-- Load More --}}
    @if($hasMoreItems)
        <div class="mt-10 text-center">
            <button
                wire:click="loadMore"
                wire:loading.attr="disabled"
                wire:target="loadMore"
                class="inline-flex items-center gap-2 px-8 py-3 border-2 border-[#c01c1c] text-[#c01c1c] text-sm font-bold rounded-lg hover:bg-[#c01c1c] hover:text-white transition-all duration-300 active:scale-[0.98] disabled:opacity-60 disabled:cursor-wait">
                <span wire:loading.remove wire:target="loadMore">আরো দেখুন</span>
                <span wire:loading wire:target="loadMore">লোড হচ্ছে...</span>
            </button>
        </div>
    @endif

    {{-- Floating Cart Bar --}}
    @if(count($cart) > 0)
        <div class="fixed bottom-4 md:bottom-6 left-1/2 -translate-x-1/2 z-50 animate-fade-in-up w-[calc(100%-1.5rem)] md:w-auto max-w-md md:max-w-none">
            <a href="/order" wire:navigate
                class="flex items-center justify-between md:justify-start gap-3 md:gap-4 bg-[#c01c1c] text-white px-5 md:px-8 py-3.5 md:py-4 rounded-2xl shadow-xl hover:bg-[#d92e2e] hover:shadow-2xl hover:-translate-y-1 transition-all active:scale-[0.98] group w-full md:w-auto">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    </div>
                    <div>
                        <span class="text-sm font-bold block">{{ $this->cartCount }} টি আইটেম</span>
                        <span class="text-xs text-white/70">৳{{ number_format($this->subtotal) }}</span>
                    </div>
                </div>
                <div class="h-8 w-px bg-white/20 mx-1 md:mx-2"></div>
                <span class="text-xs font-bold uppercase tracking-widest group-hover:translate-x-1 transition-transform whitespace-nowrap">Cart / চেকআউট →</span>
            </a>
        </div>
    @endif
</div>
