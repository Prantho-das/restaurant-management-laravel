<div class="relative">
    <!-- Category Navigation -->
    <div class="overflow-x-auto no-scrollbar -mx-4 px-4 mb-12 lg:mb-20">
        <div class="flex items-center gap-2 min-w-max">
            <button
                wire:click="selectCategory(null)"
                class="px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all duration-300 {{ is_null($selectedCategoryId) ? 'bg-brand-emerald text-white shadow-lg shadow-brand-emerald/20' : 'bg-white text-slate-500 border border-slate-200 hover:border-brand-emerald/30 hover:text-brand-emerald hover:shadow-md' }}">
                All
            </button>
            @foreach($categories as $category)
                <button
                    wire:click="selectCategory({{ $category->id }})"
                    class="px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all duration-300 {{ $selectedCategoryId == $category->id ? 'bg-brand-emerald text-white shadow-lg shadow-brand-emerald/20' : 'bg-white text-slate-500 border border-slate-200 hover:border-brand-emerald/30 hover:text-brand-emerald hover:shadow-md' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Menu Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 xl:gap-16">
        @forelse($menuItems as $index => $item)
            <div class="group relative bg-white rounded-3xl overflow-hidden shadow-card hover:shadow-2xl hover:shadow-brand-emerald/10 transition-all duration-500 flex flex-col md:flex-row card-hover" wire:key="item-{{ $item->id }}">
                <!-- Image -->
                <div class="md:w-5/12 relative aspect-square overflow-hidden">
                    <img src="{{ $item->image ? Storage::url($item->image) : asset('placeholder.png') }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-emerald/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    @if($item->discount_price && $item->discount_price < $item->base_price)
                        <span class="absolute top-3 left-3 bg-brand-red text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full shadow-lg">
                            Sale
                        </span>
                    @endif
                </div>
                
                <!-- Content -->
                <div class="md:w-7/12 p-5 lg:p-6 flex flex-col justify-center">
                    <div class="flex justify-between items-start border-b border-brand-gold/10 pb-3 lg:pb-4 mb-3 lg:mb-4">
                        <h3 class="text-lg lg:text-xl xl:text-2xl text-brand-emerald font-bold tracking-tight">{{ $item->name }}</h3>
                        <span class="text-sm lg:text-base font-black text-brand-gold whitespace-nowrap ml-4">৳{{ number_format($item->final_price, 0) }}</span>
                    </div>
                    <p class="text-brand-emerald/60 text-xs lg:text-sm leading-relaxed mb-5 lg:mb-6 line-clamp-3 font-medium">{{ $item->description }}</p>
                    
                    <button wire:click="addToCart({{ $item->id }})"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-wait"
                        class="inline-flex items-center justify-center gap-2 bg-brand-emerald/5 text-brand-emerald text-[10px] lg:text-xs font-black tracking-[0.2em] uppercase px-5 lg:px-6 py-3 lg:py-3.5 rounded-xl transition-all duration-300 hover:bg-brand-emerald hover:text-white active:scale-95">
                        <svg wire:loading.remove wire:target="addToCart({{ $item->id }})" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        <svg wire:loading wire:target="addToCart({{ $item->id }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span wire:loading wire:target="addToCart({{ $item->id }})">Adding...</span>
                        <span wire:loading.remove wire:target="addToCart({{ $item->id }})">Add to Cart</span>
                    </button>
                </div>
                
                <!-- Decorative elements -->
                <div class="absolute -bottom-8 -right-8 w-24 h-24 bg-brand-gold/5 rounded-full blur-2xl group-hover:bg-brand-gold/10 transition-colors duration-500"></div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center">
                <div class="w-20 h-20 mx-auto mb-6 bg-brand-emerald/5 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-brand-emerald/30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <h3 class="text-lg font-bold text-brand-emerald mb-2">No items found</h3>
                <p class="text-sm text-brand-emerald/50">Try a different category or check back later.</p>
            </div>
        @endforelse
    </div>

    <!-- Floating Cart Bar -->
    @if(count($cart) > 0)
        <div class="hidden md:block fixed bottom-6 left-1/2 -translate-x-1/2 z-50 animate-fade-in-up">
            <a href="/order" wire:navigate
                class="flex items-center gap-4 bg-brand-emerald text-white px-8 py-4 rounded-2xl shadow-2xl shadow-brand-emerald/30 hover:bg-brand-emerald-light hover:shadow-xl hover:-translate-y-1 transition-all active:scale-[0.98] group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    </div>
                    <div>
                        <span class="text-sm font-bold block">{{ $this->cartCount }} item(s) in cart</span>
                        <span class="text-xs text-white/70">৳{{ number_format($this->subtotal) }}</span>
                    </div>
                </div>
                <div class="h-8 w-px bg-white/20 mx-2"></div>
                <span class="text-xs font-bold uppercase tracking-widest group-hover:translate-x-1 transition-transform">Checkout →</span>
            </a>
        </div>
    @endif
</div>