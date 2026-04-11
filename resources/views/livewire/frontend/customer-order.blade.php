<div class="min-h-screen bg-brand-bg-light">

    @if(! $orderPlaced)

    {{-- ======== MOBILE CHECKOUT (direct, no product grid) ======== --}}
    <div class="lg:hidden min-h-screen bg-brand-bg-light flex flex-col">
        {{-- Mobile Header --}}
        <div class="bg-brand-primary text-white px-5 pt-14 pb-6">
            <span class="inline-block text-brand-gold font-bold tracking-[0.3em] uppercase text-[9px] mb-1">Delivery Order</span>
            <h1 class="text-2xl font-bold">Checkout</h1>
            <p class="text-white/60 text-xs mt-1">Complete your order details below</p>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-5 space-y-5 pb-32">
            @if(count($cart) > 0)
                {{-- Cart Summary --}}
                <div class="bg-white rounded-2xl border border-brand-primary/10 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-brand-primary/10 flex items-center justify-between">
                        <span class="text-xs font-bold text-brand-primary uppercase tracking-wider">Your Order</span>
                        <button wire:click="clearCart" class="text-xs text-brand-primary/50 hover:text-brand-red transition-colors font-medium">Clear All</button>
                    </div>
                    @foreach($cart as $index => $item)
                        <div class="px-4 py-3 border-b border-brand-primary/10 last:border-b-0 flex items-center gap-3" wire:key="m-cart-{{ $index }}">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-brand-text-dark truncate">{{ $item['name'] }}</h4>
                                <p class="text-xs text-brand-primary font-bold">৳{{ number_format($item['price'] * $item['quantity']) }}</p>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button wire:click="updateQuantity({{ $index }}, -1)" class="w-8 h-8 rounded-lg bg-parchment flex items-center justify-center text-brand-primary font-bold text-sm">−</button>
                                <span class="w-7 text-center text-sm font-bold text-brand-text-dark">{{ $item['quantity'] }}</span>
                                <button wire:click="updateQuantity({{ $index }}, 1)" class="w-8 h-8 rounded-lg bg-parchment flex items-center justify-center text-brand-primary font-bold text-sm">+</button>
                            </div>
                            <button wire:click="removeFromCart({{ $index }})" class="text-brand-primary/30 hover:text-brand-red p-1 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endforeach
                    <div class="px-4 py-3 bg-parchment flex justify-between items-center">
                        <span class="text-xs text-brand-text-gray font-medium">Subtotal</span>
                        <span class="text-base font-black text-brand-primary">৳{{ number_format($this->subtotal) }}</span>
                    </div>
                </div>

                {{-- Customer Details --}}
                <div class="bg-white rounded-2xl border border-brand-primary/10 shadow-sm p-4 space-y-4">
                    <h3 class="text-xs font-bold text-brand-primary uppercase tracking-wider">Your Details</h3>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-text-gray uppercase tracking-wider mb-1.5">Name <span class="text-brand-red">*</span></label>
                        <input wire:model="customerName" type="text" placeholder="Your full name"
                            class="w-full bg-parchment border border-brand-primary/15 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all">
                        @error('customerName') <span class="text-brand-red text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-text-gray uppercase tracking-wider mb-1.5">Phone <span class="text-brand-red">*</span></label>
                        <input wire:model="customerPhone" type="tel" placeholder="01XXX-XXXXXX"
                            class="w-full bg-parchment border border-brand-primary/15 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all">
                        @error('customerPhone') <span class="text-brand-red text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-text-gray uppercase tracking-wider mb-1.5">Delivery Address <span class="text-brand-red">*</span></label>
                        <textarea wire:model="deliveryAddress" rows="2" placeholder="Full delivery address..."
                            class="w-full bg-parchment border border-brand-primary/15 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all resize-none"></textarea>
                        @error('deliveryAddress') <span class="text-brand-red text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="bg-white rounded-2xl border border-brand-primary/10 shadow-sm p-4">
                    <h3 class="text-xs font-bold text-brand-primary uppercase tracking-wider mb-3">Payment Method</h3>
                    <div class="grid grid-cols-3 gap-2">
                        <button wire:click="$set('paymentMethod', 'cash')"
                            class="py-3 rounded-xl flex flex-col items-center gap-1.5 transition-all {{ $paymentMethod === 'cash' ? 'bg-brand-primary text-white shadow-lg' : 'bg-parchment text-brand-primary/60 border border-brand-primary/15' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            <span class="text-[10px] font-bold uppercase">Cash</span>
                        </button>
                        <button wire:click="$set('paymentMethod', 'mobile_pay')"
                            class="py-3 rounded-xl flex flex-col items-center gap-1.5 transition-all {{ $paymentMethod === 'mobile_pay' ? 'bg-sky-500 text-white shadow-lg' : 'bg-parchment text-brand-primary/60 border border-brand-primary/15' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            <span class="text-[10px] font-bold uppercase">bKash</span>
                        </button>
                        <button wire:click="$set('paymentMethod', 'card')"
                            class="py-3 rounded-xl flex flex-col items-center gap-1.5 transition-all {{ $paymentMethod === 'card' ? 'bg-cyan-500 text-white shadow-lg' : 'bg-parchment text-brand-primary/60 border border-brand-primary/15' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                            <span class="text-[10px] font-bold uppercase">Card</span>
                        </button>
                    </div>
                    @if($paymentMethod !== 'cash')
                        <div class="mt-3 px-4 py-3 rounded-xl bg-parchment border border-brand-primary/10 flex items-start gap-3">
                            <svg class="w-4 h-4 text-brand-primary/50 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            <p class="text-xs text-brand-text-gray leading-snug">You will be securely redirected to the <strong class="text-brand-primary">{{ $paymentMethod === 'mobile_pay' ? 'bKash' : 'SSLCommerz' }}</strong> gateway.</p>
                        </div>
                    @endif
                </div>

                {{-- Special Instructions --}}
                <div class="bg-white rounded-2xl border border-brand-primary/10 shadow-sm p-4">
                    <label class="block text-xs font-bold text-brand-primary uppercase tracking-wider mb-2">Special Instructions</label>
                    <textarea wire:model="notes" rows="2" placeholder="Allergies, spice level, etc."
                        class="w-full bg-parchment border border-brand-primary/15 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all resize-none"></textarea>
                </div>

            @else
                {{-- Empty Cart State --}}
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-20 h-20 bg-brand-primary/5 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-brand-primary/30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-brand-primary mb-2">Your cart is empty</h3>
                    <p class="text-sm text-brand-text-gray mb-6">Browse our menu on your desktop or visit us to place an order.</p>
                    <a href="/menu" wire:navigate class="px-6 py-3 bg-brand-primary text-white text-xs font-bold uppercase tracking-widest rounded-xl shadow-lg shadow-brand-primary/20">View Menu</a>
                </div>
            @endif
        </div>

        {{-- Sticky Place Order Button --}}
        @if(count($cart) > 0)
            <div class="fixed bottom-0 left-0 right-0 px-4 py-4 bg-white/90 backdrop-blur-md border-t border-brand-primary/10 z-40">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-xs text-brand-text-gray font-medium">{{ $this->cartCount }} item(s)</span>
                    <span class="text-lg font-black text-brand-primary">৳{{ number_format($this->subtotal) }}</span>
                </div>
                <button wire:click="placeOrder"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-wait"
                    class="w-full py-4 bg-brand-primary text-white text-sm font-bold uppercase tracking-widest rounded-2xl shadow-lg shadow-brand-primary/30 flex items-center justify-center gap-2 active:scale-[0.98] transition-all">
                    <span wire:loading.remove wire:target="placeOrder">
                        <svg class="w-5 h-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Place Delivery Order
                    </span>
                    <span wire:loading wire:target="placeOrder">
                        <svg class="animate-spin w-5 h-5 inline mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Processing...
                    </span>
                </button>
            </div>
        @endif
    </div>
    {{-- END MOBILE CHECKOUT --}}

    {{-- ======== DESKTOP ORDER VIEW (unchanged) ======== --}}
    <div class="hidden lg:block">
    <div class="container-wide pt-28 pb-24">
        {{-- Header --}}
        <div class="text-center mb-12">
            <span class="inline-block text-brand-gold font-bold tracking-[0.4em] uppercase text-[10px] mb-3">Delivery Order</span>
            <h1 class="text-5xl font-bold text-brand-primary mb-3">Order <span class="text-brand-gold italic">Delicious</span> Food</h1>
            <p class="text-brand-text-gray text-sm max-w-md mx-auto">Browse our menu, add items to your cart, and place your delivery order.</p>
        </div>

        <div class="flex flex-row gap-10">
            {{-- LEFT: Menu Grid --}}
            <div class="flex-1 min-w-0">
                {{-- Search --}}
                <div class="mb-6">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-brand-primary/40">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search for your favourite dish..."
                            class="w-full bg-white border border-brand-primary/15 rounded-2xl py-3 pl-12 pr-4 text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all shadow-sm">
                    </div>
                </div>

                {{-- Category Pills --}}
                <div class="flex flex-wrap gap-2 mb-6">
                    <button wire:click="selectCategory(null)"
                        class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider transition-all {{ !$selectedCategoryId ? 'bg-brand-primary text-white shadow-lg shadow-brand-primary/20' : 'bg-white text-brand-primary/60 border border-brand-primary/15 hover:border-brand-primary/30 hover:text-brand-primary' }}">
                        All
                    </button>
                    @foreach($categories as $category)
                        <button wire:click="selectCategory({{ $category->id }})"
                            class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider transition-all {{ $selectedCategoryId == $category->id ? 'bg-brand-primary text-white shadow-lg shadow-brand-primary/20' : 'bg-white text-brand-primary/60 border border-brand-primary/15 hover:border-brand-primary/30 hover:text-brand-primary' }}">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>

                {{-- Products Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 lg:gap-6">
                    @forelse($items as $item)
                        <div class="group relative bg-white rounded-2xl overflow-hidden shadow-card hover:shadow-2xl hover:shadow-brand-primary/10 border border-brand-primary/10 hover:border-brand-primary/20 transition-all duration-300" wire:key="menu-{{ $item->id }}">
                            <div class="relative aspect-[4/3] overflow-hidden bg-parchment">
                                <img src="{{ $item->image ? Storage::url($item->image) : asset('placeholder.png') }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $item->name }}">
                                <div class="absolute inset-0 bg-gradient-to-t from-brand-primary/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <div class="absolute top-3 right-3">
                                    <span class="bg-white/95 backdrop-blur-sm px-3 py-1.5 rounded-full text-xs font-bold text-brand-primary shadow-sm">
                                        ৳{{ number_format($item->final_price) }}
                                    </span>
                                </div>
                                @if($item->discount_price && $item->discount_price < $item->base_price)
                                    <div class="absolute top-3 left-3">
                                        <span class="bg-brand-red text-white px-2.5 py-1 rounded-full text-[10px] font-bold uppercase">Sale</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <p class="text-[10px] font-bold text-brand-gold uppercase tracking-wider mb-1">{{ $item->category->name ?? '' }}</p>
                                <h3 class="font-bold text-brand-text-dark text-sm mb-2 line-clamp-1">{{ $item->name }}</h3>
                                @if($item->description)
                                    <p class="text-xs text-brand-text-gray mb-4 line-clamp-2">{{ $item->description }}</p>
                                @else
                                    <div class="mb-4"></div>
                                @endif
                                <button wire:click="addToCart({{ $item->id }})"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-70 cursor-wait"
                                    class="w-full py-2.5 bg-[#c01c1c] text-white text-xs font-bold uppercase tracking-wider rounded-xl hover:bg-[#d92e2e] transition-all active:scale-[0.97] flex items-center justify-center gap-2">
                                    <svg wire:loading.remove wire:target="addToCart({{ $item->id }})" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                    <svg wire:loading wire:target="addToCart({{ $item->id }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    <span wire:loading wire:target="addToCart({{ $item->id }})">Adding...</span>
                                    <span wire:loading.remove wire:target="addToCart({{ $item->id }})">কার্ট</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 bg-brand-primary/5 rounded-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-brand-primary/30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </div>
                            <h3 class="font-bold text-brand-primary mb-1">No items found</h3>
                            <p class="text-xs text-brand-text-gray">Try a different search or category.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- RIGHT: Checkout Sidebar (Desktop) --}}
            <div class="w-[380px] shrink-0">
                <div class="sticky top-28 bg-white rounded-2xl border border-brand-primary/10 shadow-card overflow-hidden max-h-[calc(100vh-8rem)] flex flex-col">
                    {{-- Cart Header --}}
                    <div class="px-5 py-4 border-b border-brand-primary/10 flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-brand-primary/10 flex items-center justify-center text-brand-primary">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-brand-primary">Checkout</h3>
                                <span class="text-[10px] text-brand-text-gray font-medium">{{ $this->cartCount }} item(s) · Delivery</span>
                            </div>
                        </div>
                        @if(count($cart) > 0)
                            <button wire:click="clearCart" class="text-xs text-brand-primary/50 hover:text-brand-red transition-colors font-medium">Clear</button>
                        @endif
                    </div>

                    {{-- Scrollable Content --}}
                    <div class="flex-1 overflow-y-auto">
                        @if(count($cart) > 0)
                            {{-- Cart Items --}}
                            <div class="border-b border-brand-primary/10">
                                @foreach($cart as $index => $item)
                                    <div class="px-5 py-3 border-b border-brand-primary/10 last:border-b-0 flex items-center gap-3" wire:key="cart-{{ $index }}">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-xs font-bold text-brand-text-dark truncate">{{ $item['name'] }}</h4>
                                            <p class="text-xs text-brand-primary font-bold">৳{{ number_format($item['price'] * $item['quantity']) }}</p>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button wire:click="updateQuantity({{ $index }}, -1)" class="w-7 h-7 rounded-lg bg-parchment hover:bg-brand-primary/10 flex items-center justify-center text-brand-primary/70 text-xs font-bold transition-colors">−</button>
                                            <span class="w-7 text-center text-xs font-bold text-brand-text-dark">{{ $item['quantity'] }}</span>
                                            <button wire:click="updateQuantity({{ $index }}, 1)" class="w-7 h-7 rounded-lg bg-parchment hover:bg-brand-primary/10 flex items-center justify-center text-brand-primary/70 text-xs font-bold transition-colors">+</button>
                                        </div>
                                        <button wire:click="removeFromCart({{ $index }})" class="text-brand-primary/30 hover:text-brand-red transition-colors p-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Checkout Form --}}
                            <div class="p-5 space-y-4">
                                {{-- Customer Details --}}
                                <div>
                                    <label class="block text-[10px] font-bold text-brand-text-gray uppercase tracking-wider mb-1.5">Name <span class="text-brand-red">*</span></label>
                                    <input wire:model="customerName" type="text" placeholder="Your full name"
                                        class="w-full bg-parchment border border-brand-primary/15 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all">
                                    @error('customerName') <span class="text-brand-red text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-brand-text-gray uppercase tracking-wider mb-1.5">Phone <span class="text-brand-red">*</span></label>
                                    <input wire:model="customerPhone" type="tel" placeholder="01XXX-XXXXXX"
                                        class="w-full bg-parchment border border-brand-primary/15 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all">
                                    @error('customerPhone') <span class="text-brand-red text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                                </div>

                                {{-- Delivery Address --}}
                                <div>
                                    <label class="block text-[10px] font-bold text-brand-text-gray uppercase tracking-wider mb-1.5">Delivery Address <span class="text-brand-red">*</span></label>
                                    <textarea wire:model="deliveryAddress" rows="2" placeholder="Full delivery address..."
                                        class="w-full bg-parchment border border-brand-primary/15 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all resize-none"></textarea>
                                    @error('deliveryAddress') <span class="text-brand-red text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                                </div>

                                {{-- Payment Method --}}
                                <div>
                                    <label class="block text-[10px] font-bold text-brand-text-gray uppercase tracking-wider mb-2">Payment</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        <button wire:click="$set('paymentMethod', 'cash')"
                                            class="py-2.5 rounded-xl flex flex-col items-center gap-1.5 transition-all {{ $paymentMethod === 'cash' ? 'bg-brand-primary text-white shadow-lg shadow-brand-primary/20' : 'bg-parchment text-brand-primary/60 border border-brand-primary/15 hover:border-brand-primary/30' }}">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                            <span class="text-[9px] font-bold uppercase tracking-wider">Cash</span>
                                        </button>
                                        <button wire:click="$set('paymentMethod', 'mobile_pay')"
                                            class="py-2.5 rounded-xl flex flex-col items-center gap-1.5 transition-all {{ $paymentMethod === 'mobile_pay' ? 'bg-sky-500 text-white shadow-lg shadow-sky-500/20' : 'bg-parchment text-brand-primary/60 border border-brand-primary/15 hover:border-sky-500/30' }}">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                            <span class="text-[9px] font-bold uppercase tracking-wider">bKash</span>
                                        </button>
                                        <button wire:click="$set('paymentMethod', 'card')"
                                            class="py-2.5 rounded-xl flex flex-col items-center gap-1.5 transition-all {{ $paymentMethod === 'card' ? 'bg-cyan-500 text-white shadow-lg shadow-cyan-500/20' : 'bg-parchment text-brand-primary/60 border border-brand-primary/15 hover:border-cyan-500/30' }}">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                                            <span class="text-[9px] font-bold uppercase tracking-wider">Card</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Gateway Info --}}
                                @if($paymentMethod !== 'cash')
                                    <div class="px-3.5 py-3 rounded-xl bg-parchment border border-brand-primary/10 flex items-start gap-2.5">
                                        <svg class="w-4 h-4 text-brand-primary/50 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                        <p class="text-[10px] text-brand-text-gray leading-tight">You will be securely redirected to the <strong class="text-brand-primary">{{ $paymentMethod === 'mobile_pay' ? 'bKash' : 'SSLCommerz' }}</strong> gateway.</p>
                                    </div>
                                @endif

                                {{-- Notes --}}
                                <div>
                                    <label class="block text-[10px] font-bold text-brand-text-gray uppercase tracking-wider mb-1.5">Special Instructions</label>
                                    <textarea wire:model="notes" rows="2" placeholder="Allergies, spice level, etc."
                                        class="w-full bg-parchment border border-brand-primary/15 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all resize-none"></textarea>
                                </div>
                            </div>
                        @else
                            {{-- Empty Cart --}}
                            <div class="py-16 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 bg-brand-primary/5 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-brand-primary/30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                </div>
                                <p class="text-sm text-brand-text-gray font-medium">Your cart is empty</p>
                                <p class="text-xs text-brand-text-gray/40 mt-1">Add items from the menu to get started</p>
                            </div>
                        @endif
                    </div>

                    {{-- Footer: Total + Place Order --}}
                    @if(count($cart) > 0)
                        <div class="px-5 py-4 bg-parchment border-t border-brand-primary/10 shrink-0">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-xs font-medium text-brand-text-gray uppercase tracking-wider">Total</span>
                                <span class="text-lg font-bold text-brand-primary">৳{{ number_format($this->subtotal) }}</span>
                            </div>
                            <button wire:click="placeOrder"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-60 cursor-wait"
                                class="w-full py-3 bg-brand-primary text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-brand-primary-light transition-all shadow-lg shadow-brand-primary/20 active:scale-[0.97] flex items-center justify-center gap-2">
                                <span wire:loading.remove wire:target="placeOrder">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Place Delivery Order
                                </span>
                                <span wire:loading wire:target="placeOrder">
                                    <svg class="animate-spin w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>{{-- end desktop wrapper --}}

    @else
    {{-- ======== ORDER CONFIRMED ======== --}}
    <div class="container-wide pt-24 pb-32 lg:pt-28 lg:pb-20">
        <div class="max-w-lg mx-auto text-center animate-fade-in-up">
            <div class="bg-white rounded-3xl p-8 lg:p-10 border border-brand-primary/10 shadow-card">
                <div class="w-20 h-20 mx-auto mb-6 bg-brand-primary/10 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>

                <h1 class="text-2xl font-bold text-brand-primary mb-2">Order Placed Successfully!</h1>
                <p class="text-sm text-brand-text-gray mb-8">Thank you for your order. We'll deliver it to your address shortly.</p>

                <div class="bg-parchment rounded-2xl p-6 mb-8 text-left">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-xs font-medium text-brand-text-gray uppercase tracking-wider">Order Number</span>
                        <span class="text-sm font-bold text-brand-primary">{{ $confirmedOrderNumber }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-xs font-medium text-brand-text-gray uppercase tracking-wider">Total Amount</span>
                        <span class="text-sm font-bold text-brand-primary">৳{{ number_format($confirmedTotal) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-brand-text-gray uppercase tracking-wider">Status</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">
                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                            Pending Confirmation
                        </span>
                    </div>
                </div>

                @if(!empty($confirmedOrderItems))
                    <div class="bg-parchment rounded-2xl p-6 mb-8 text-left">
                        <h3 class="text-xs font-bold text-brand-text-gray uppercase tracking-wider border-b border-brand-primary/10 pb-3 mb-4">Ordered Items</h3>
                        <div class="space-y-3">
                            @foreach($confirmedOrderItems as $item)
                                <div class="flex justify-between items-center" wire:key="confirmed-item-{{ $item['id'] }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 rounded bg-brand-primary/10 flex items-center justify-center text-[10px] font-bold text-brand-primary">
                                            {{ $item['quantity'] }}x
                                        </div>
                                        <span class="text-sm font-medium text-brand-text-dark">{{ $item['name'] }}</span>
                                    </div>
                                    <span class="text-sm font-bold text-brand-primary">৳{{ number_format($item['price'] * $item['quantity']) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <p class="text-xs text-brand-text-gray/40 mb-6">Please save your order number for reference. You will receive confirmation shortly.</p>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button wire:click="startNewOrder"
                        class="flex-1 py-3 bg-brand-primary text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-brand-primary-light transition-all shadow-lg shadow-brand-primary/20">
                        Order More
                    </button>
                    <a href="/" wire:navigate
                        class="flex-1 py-3 bg-parchment text-brand-primary text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-brand-primary/10 transition-all text-center">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

</div>
