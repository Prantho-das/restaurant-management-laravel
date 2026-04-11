<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
    @foreach($signatureMenuItems as $index => $item)
        <div class="group relative bg-white rounded-2xl overflow-hidden shadow-card hover:shadow-2xl hover:shadow-brand-emerald/10 transition-all duration-300" wire:key="featured-{{ $item->id }}">
            <!-- Image Container -->
            <div class="relative aspect-square overflow-hidden">
                <img src="{{ $item->image ? Storage::url($item->image) : asset('placeholder.png') }}" 
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                
                <!-- Gradient Overlay on Hover -->
                <div class="absolute inset-0 bg-gradient-to-t from-brand-emerald/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                
                <!-- Price Badge -->
                <div class="absolute top-3 right-3 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-xl shadow-md">
                    <span class="text-sm font-bold tracking-wide text-brand-emerald">৳ {{ number_format($item->final_price, 0) }}</span>
                </div>
                
                <!-- Sale Badge -->
                @if($item->discount_price && $item->discount_price < $item->base_price)
                    <div class="absolute top-3 left-3">
                        <span class="bg-brand-red text-white px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider shadow-md">Sale</span>
                    </div>
                @endif
            </div>
            
            <!-- Content -->
            <div class="p-4 lg:p-5">
                <h3 class="text-base lg:text-lg text-brand-emerald font-bold leading-tight mb-2">{{ $item->name }}</h3>
                <p class="text-brand-emerald/60 text-sm mb-4 line-clamp-2">{{ $item->description }}</p>

                <!-- Cart Button -->
                <button wire:click="addToCart({{ $item->id }})" 
                    wire:loading.attr="disabled"
                    class="w-full py-2.5 bg-[#c01c1c] text-white text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-[#d92e2e] transition-all active:scale-[0.97] flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-wait">
                    <svg wire:loading.remove wire:target="addToCart({{ $item->id }})" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    <svg wire:loading wire:target="addToCart({{ $item->id }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span wire:loading wire:target="addToCart({{ $item->id }})">Adding...</span>
                    <span wire:loading.remove wire:target="addToCart({{ $item->id }})">কার্ট</span>
                </button>
            </div>
            
            <!-- Decorative corner -->
            <div class="absolute -bottom-4 -right-4 w-16 h-16 bg-brand-gold/5 rounded-full blur-xl"></div>
        </div>
    @endforeach
</div>
