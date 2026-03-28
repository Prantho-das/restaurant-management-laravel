<div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12">
    @foreach($signatureMenuItems as $item)
        <!-- Item -->
        <div class="group animate-fade-in-up bg-bg-light p-6 rounded-3xl border border-brand-accent/5 hover:border-brand-accent/20 transition-all shadow-sm" wire:key="featured-{{ $item->id }}">
            <div class="relative mb-8 overflow-hidden rounded-2xl aspect-[4/5] shadow-premium">
                <img src="/images/placeholders/{{ $item->image ?? 'kacchi_biryani_1774629083139.png' }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-md px-4 py-2 rounded-full text-[10px] font-black tracking-widest text-brand-primary">৳ {{ number_format($item->final_price, 0) }}</div>
                @if($item->discount_price && $item->discount_price < $item->base_price)
                    <div class="absolute top-4 left-4">
                        <span class="bg-brand-red text-white px-3 py-1 rounded-full text-[9px] font-bold uppercase">Sale</span>
                    </div>
                @endif
            </div>
            <h3 class="text-2xl mb-2 text-slate-900 font-bold">{{ $item->name }}</h3>
            <p class="text-slate-500 text-xs mb-8 leading-relaxed">{{ $item->description }}</p>

            <!-- Dynamic Livewire Add to Cart Button -->
            <button wire:click="addToCart({{ $item->id }})" class="btn-brand w-full text-center text-[10px] uppercase tracking-widest py-3 flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="addToCart({{ $item->id }})" class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    Add to Cart
                </span>
                <span wire:loading wire:target="addToCart({{ $item->id }})">Adding...</span>
            </button>
        </div>
    @endforeach
</div>
