<div>
    <!-- Categories -->
    <div class="overflow-x-auto no-scrollbar -mx-4 px-4 mb-20 animate-fade-in-up delay-100">
        <div class="flex items-center gap-8 md:gap-12 min-w-max md:justify-center">
            <button
                wire:click="selectCategory(null)"
                class="text-[10px] font-black tracking-[0.3em] uppercase pb-2 border-b-2 transition-all duration-300 {{ is_null($selectedCategoryId) ? 'border-brand-gold text-brand-emerald text-[11px]' : 'border-transparent text-slate-400 hover:text-slate-700' }}">
                All Collections
            </button>
            @foreach($categories as $category)
                <button
                    wire:click="selectCategory({{ $category->id }})"
                    class="text-[10px] font-black tracking-[0.3em] uppercase pb-2 border-b-2 transition-all duration-300 {{ $selectedCategoryId == $category->id ? 'border-brand-gold text-brand-emerald text-[11px]' : 'border-transparent text-slate-400 hover:text-slate-700' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Menu Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-16 md:gap-x-24 md:gap-y-32">
        @foreach($menuItems as $item)
            <div class="group flex flex-col sm:flex-row gap-6 md:gap-12 animate-fade-in-up" wire:key="item-{{ $item->id }}">
                <div class="sm:w-1/2 aspect-square md:aspect-[4/5] rounded-2xl md:rounded-3xl overflow-hidden shadow-premium relative">
                    <img src="{{ Str::startsWith($item->image, 'http') ? $item->image : Storage::url($item->image ?? 'images/placeholders/kacchi_biryani_1774629083139.png') }}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent md:hidden opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <div class="sm:w-1/2 flex flex-col justify-center">
                    <div class="flex justify-between items-baseline border-b border-brand-gold/10 pb-3 mb-4">
                        <h3 class="text-xl md:text-3xl text-brand-emerald font-bold tracking-tight">{{ $item->name }}</h3>
                        <span class="text-sm font-black text-brand-gold">৳{{ number_format($item->final_price, 0) }}</span>
                    </div>
                    <p class="text-brand-emerald/60 text-xs md:text-sm leading-relaxed mb-6 line-clamp-3 font-medium">{{ $item->description }}</p>
                    <button wire:click="addToCart({{ $item->id }})"
                        x-data="{ clicking: false }"
                        @click="clicking = true; setTimeout(() => clicking = false, 500)"
                        class="inline-flex items-center justify-center gap-2 bg-brand-emerald/5 text-brand-emerald text-[10px] font-black tracking-[0.2em] uppercase px-6 py-3.5 rounded-xl transition-all duration-300 hover:bg-brand-emerald hover:text-white active:scale-95 relative overflow-hidden">
                        <svg :class="clicking ? 'scale-150 rotate-12' : ''" class="w-4 h-4 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        <span x-text="clicking ? 'Adding...' : 'Add to Cart'"></span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Floating Cart Bar -->
    @if(count($cart) > 0)
        <div class="hidden md:block fixed bottom-6 left-1/2 -translate-x-1/2 z-50 animate-fade-in-up">
            <a href="/order" wire:navigate
                class="flex items-center gap-4 bg-brand-emerald text-white px-8 py-4 rounded-2xl shadow-2xl shadow-brand-emerald/30 hover:bg-brand-emerald/90 transition-all active:scale-[0.98]">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold block">{{ $this->cartCount }} item(s) in cart</span>
                        <span class="text-[10px] text-white/70">৳{{ number_format($this->subtotal) }}</span>
                    </div>
                </div>
                <div class="h-8 w-px bg-white/20 mx-2"></div>
                <span class="text-xs font-bold uppercase tracking-widest">Checkout →</span>
            </a>
        </div>
    @endif
</div>
