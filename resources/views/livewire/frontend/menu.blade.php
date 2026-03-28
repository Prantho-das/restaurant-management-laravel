<div>
    <!-- Categories -->
    <div class="flex flex-wrap justify-center gap-10 mb-32 animate-fade-in-up delay-100">
        <button
            wire:click="selectCategory(null)"
            class="text-[11px] font-black tracking-[0.3em] uppercase pb-2 border-b-2 {{ is_null($selectedCategoryId) ? 'border-brand-accent text-slate-900' : 'border-transparent text-slate-400 hover:text-slate-700' }} transition-colors">
            All Collections
        </button>
        @foreach($categories as $category)
            <button
                wire:click="selectCategory({{ $category->id }})"
                class="text-[11px] font-black tracking-[0.3em] uppercase pb-2 border-b-2 {{ $selectedCategoryId == $category->id ? 'border-brand-accent text-slate-900' : 'border-transparent text-slate-400 hover:text-slate-700' }} transition-colors">
                {{ $category->name }}
            </button>
        @endforeach
    </div>

    <!-- Menu Grid -->
    <div class="grid lg:grid-cols-2 gap-x-24 gap-y-32">
        @foreach($menuItems as $item)
            <div class="flex flex-col md:flex-row gap-12 animate-fade-in-up" wire:key="item-{{ $item->id }}">
                <div class="md:w-1/2 aspect-[4/5] rounded-2xl overflow-hidden shadow-premium">
                    <img src="/images/placeholders/{{ $item->image ?? 'kacchi_biryani_1774629083139.png' }}" class="w-full h-full object-cover">
                </div>
                <div class="md:w-1/2 flex flex-col justify-center">
                    <div class="flex justify-between items-baseline border-b border-brand-accent/20 pb-4 mb-6">
                        <h3 class="text-3xl text-slate-900 font-bold">{{ $item->name }}</h3>
                        <span class="text-sm font-bold text-brand-accent">৳ {{ number_format($item->final_price, 0) }}</span>
                    </div>
                    <p class="text-slate-600 text-sm leading-loose mb-8">{{ $item->description }}</p>
                    <button wire:click="addToCart({{ $item->id }})"
                        class="inline-flex items-center justify-center gap-2 bg-brand-primary/5 text-brand-primary text-[10px] font-black tracking-[0.2em] uppercase px-6 py-3 rounded-xl hover:bg-brand-primary hover:text-white transition-all active:scale-[0.97]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Add to Cart
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Floating Cart Bar -->
    @if(count($cart) > 0)
        <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 animate-fade-in-up">
            <a href="/order"
                class="flex items-center gap-4 bg-brand-primary text-white px-8 py-4 rounded-2xl shadow-2xl shadow-brand-primary/30 hover:bg-brand-primary-dark transition-all active:scale-[0.98]">
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
