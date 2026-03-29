<div>
    <!-- CSS Loading Screen (Prevents FOUC from Vite in dev mode) -->
    <div class="!hidden" style="position: fixed; inset: 0; background-color: #F9FAFB; z-index: 99999; display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <svg style="width: 48px; height: 48px; color: #808000; animation: pos-spin 1s linear infinite;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p style="margin-top: 16px; font-family: 'Inter', sans-serif; font-weight: 900; color: #808000; font-size: 12px; letter-spacing: 0.2em; text-transform: uppercase;">Booting POS...</p>
        <style>
            @keyframes pos-spin { 100% { transform: rotate(360deg); } }
        </style>
    </div>

    <script>
        function posSystemData() {
            return {
                isOnline: navigator.onLine,
                syncing: false,
                db: null,
                lastReceipt: null,
                showSuccessModal: false,
                init() {
                    this.db = new Dexie('POS_Offline_DB');
                    this.db.version(2).stores({
                        orders: '++id, cart, total, timestamp, details, synced'
                    });
                    window.addEventListener('online', () => {
                        this.isOnline = true;
                        this.syncOrders();
                    });
                    window.addEventListener('offline', () => {
                        this.isOnline = false;
                    });
                    if (this.isOnline) {
                        this.syncOrders();
                    }
                },
                async saveOfflineOrder(cart, total, details) {
                    await this.db.orders.add({
                        cart: JSON.parse(JSON.stringify(cart)),
                        total: total,
                        details: details,
                        timestamp: new Date().toISOString(),
                        synced: 0
                    });
                    console.log('Order saved locally (offline mode)');
                },
                async syncOrders() {
                    const unsyncedOrders = await this.db.orders.where('synced').equals(0).toArray();
                    if (unsyncedOrders.length === 0) return;
                    this.syncing = true;
                    const results = { success: 0, failed: 0 };
                    for (const order of unsyncedOrders) {
                        try {
                            const response = await fetch('/api/offline/queue', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                                },
                                body: JSON.stringify({
                                    order_number: order.details.order_number || 'ORD-OFF-' + Math.random().toString(36).substr(2, 9).toUpperCase(),
                                    subtotal_amount: order.total,
                                    total_amount: order.total,
                                    order_type: order.details.order_type || 'takeaway',
                                    payment_method: order.details.payment_method || 'cash',
                                    table_number: order.details.table_number || null,
                                    customer_name: order.details.customer_name || null,
                                    customer_phone: order.details.customer_phone || null,
                                    guest_count: order.details.guest_count || 1,
                                    notes: (order.details.notes || '') + ' (Synced from offline)',
                                    reference_no: order.details.reference_no || null,
                                    items: order.cart
                                })
                            });
                            const data = await response.json();
                            if (data.success) {
                                await this.db.orders.update(order.id, { synced: 1 });
                                results.success++;
                                console.log('Order synced successfully:', data.data.order_number);
                            } else {
                                results.failed++;
                                console.error('Failed to sync order:', data.message);
                            }
                        } catch (e) {
                            results.failed++;
                            console.error('Network error syncing order:', e);
                        }
                    }
                    this.syncing = false;
                    if (results.success > 0) {
                        if (typeof $wire !== 'undefined') {
                            $wire.dispatch('notify', { type: 'success', message: `${results.success} order(s) synced successfully!` });
                        }
                    }
                    if (results.failed > 0) {
                        if (typeof $wire !== 'undefined') {
                            $wire.dispatch('notify', { type: 'error', message: `${results.failed} order(s) failed to sync. Will retry later.` });
                        }
                    }
                    if (this.isOnline && results.success > 0) {
                        fetch('/api/offline/sync', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                'Content-Type': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    console.log('Offline sync queue processed:', data.data);
                                }
                            })
                            .catch(e => console.error('Failed to process sync queue:', e));
                    }
                    if (typeof $wire !== 'undefined' && $wire.checkOfflineStatus) {
                        $wire.checkOfflineStatus();
                    }
                }
            };
        }
    </script>

    <div x-data="posSystemData()"
        x-on:order-placed.window="if (!isOnline) { await saveOfflineOrder($wire.cart, $wire.total, { order_number: $event.detail?.order_number, order_type: $wire.orderType, payment_method: $wire.paymentMethod, table_number: $wire.tableNumber, customer_name: $wire.customerName, customer_phone: $wire.customerPhone, guest_count: $wire.guestCount, notes: $wire.notes, reference_no: $wire.referenceNo }); } else { syncOrders(); }"
        x-on:pos-receipt-ready.window="lastReceipt = $event.detail; showSuccessModal = true"
        style="display: none;"
        class="!flex flex h-full bg-[#F9FAFB] overflow-hidden font-sans relative text-[11px]"
        :class="{ 'overflow-hidden': $wire.showCart }">

        <!-- Offline Sync Status Indicator -->
        <div class="fixed top-4 right-4 z-50">
            @livewire('offline-sync-status')
        </div>

        <!-- Mobile Cart Toggle -->
        <button @click="$wire.showCart = true"
            class="lg:hidden fixed bottom-6 right-6 z-50 w-16 h-16 bg-brand-primary text-white rounded-full shadow-2xl flex items-center justify-center animate-bounce">
            <div class="relative">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <template x-if="$wire.cart.length > 0">
                    <span
                        class="absolute -top-2 -right-2 bg-rose-500 text-white text-[10px] font-black w-6 h-6 rounded-full flex items-center justify-center border-2 border-white"
                        x-text="$wire.cart.length"></span>
                </template>
            </div>
        </button>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0">
            <!-- Modern Search & Status Header -->
            <div
                class="bg-white px-3 md:px-5 py-2.5 border-b border-slate-100 flex flex-col md:flex-row items-center gap-3">
                <div class="w-full md:flex-1 relative group">
                    <div
                        class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-primary transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search menu items..."
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2 pl-10 pr-4 text-[10px] focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all">
                </div>

                <!-- Global Status Toggles -->
                <div class="flex items-center gap-3">
                    <div x-show="!isOnline"
                        class="flex items-center gap-1.5 bg-rose-50 text-rose-600 px-3 py-1.5 rounded-lg border border-rose-100 text-[9px] font-bold uppercase tracking-wider animate-pulse">
                        <span class="w-1.5 h-1.5 bg-rose-600 rounded-full"></span> Offline
                    </div>
                    <div x-show="syncing"
                        class="flex items-center gap-1.5 text-brand-primary text-[9px] font-bold uppercase tracking-wider">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Syncing...
                    </div>
                </div>
            </div>

            <div
                class="bg-white px-3 md:px-5 py-2 border-b border-slate-100 flex items-center gap-2 overflow-x-auto scrollbar-hide">
                <button wire:click="selectCategory(null)"
                    class="px-4 py-1.5 rounded-xl text-[9px] font-bold uppercase tracking-wider transition-all flex-shrink-0 {{ !$selectedCategoryId ? 'bg-brand-primary text-white shadow-lg' : 'bg-slate-50 text-slate-500 hover:bg-slate-100' }}">
                    All Items
                </button>
                @foreach($categories as $category)
                    <button wire:key="category-{{ $category->id }}" wire:click="selectCategory({{ $category->id }})"
                        class="px-4 py-1.5 rounded-xl text-[9px] font-bold uppercase tracking-wider transition-all flex-shrink-0 {{ $selectedCategoryId == $category->id ? 'bg-brand-primary text-white shadow-lg' : 'bg-slate-50 text-slate-500 hover:bg-slate-100' }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                <div
                    class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 px-1 md:px-0">
                    @foreach($items as $item)
                        <div wire:key="item-{{ $item->id }}" wire:click="addToCart({{ $item->id }})"
                            class="group bg-white rounded-2xl p-3 shadow-sm border border-slate-200 transition-all hover:shadow-lg hover:border-brand-primary/20 flex flex-col h-full relative cursor-pointer active:scale-[0.98]">
                            <div class="relative aspect-[4/3] rounded-xl overflow-hidden mb-3 bg-slate-50">
                                <img src="{{ $item->image ?? '/images/placeholders/kacchi_biryani_1774629083139.png' }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">

                                <!-- Price Float -->
                                <div class="absolute top-2 right-2">
                                    <span
                                        class="bg-white/90 backdrop-blur-md px-2 py-0.5 rounded-lg text-[10px] font-black text-brand-primary shadow-lg border border-brand-primary/10">
                                        ৳{{ number_format($item->final_price) }}
                                    </span>
                                </div>

                                @if($item->available_stock <= 0)
                                    <div
                                        class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center">
                                        <span
                                            class="bg-rose-500 text-white text-[8px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full shadow-2xl">Out
                                            of Stock</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 px-0.5">
                                <h3
                                    class="text-[10px] font-black text-slate-800 uppercase tracking-tight mb-0.5 group-hover:text-brand-primary transition-colors line-clamp-1">
                                    {{ $item->name }}
                                </h3>
                                <p class="text-[8px] text-slate-400 font-bold mb-1.5 italic">{{ $item->category->name }}</p>

                                <div class="mt-auto flex items-center justify-between gap-2">
                                    @if($item->available_stock > 0 && $item->available_stock < 5)
                                        <span class="text-rose-500 text-[7px] font-black uppercase tracking-tighter">Only
                                            {{ $item->available_stock }} Left</span>
                                    @elseif($item->available_stock > 0)
                                        <div class="flex items-center gap-1">
                                            <div class="w-1.5 h-1.5 bg-brand-primary rounded-full"></div>
                                            <span class="text-[7px] font-bold text-slate-400 uppercase">In Stock</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </main>

        <!-- Detailed Sidebar: Current Order -->
        <aside
            class="fixed inset-y-0 right-0 w-full md:w-[380px] bg-white border-l border-slate-100 flex flex-col shadow-2xl z-50 transform transition-transform duration-500 ease-in-out lg:relative lg:translate-x-0"
            :class="{ 'translate-x-0': $wire.showCart, 'translate-x-full': !$wire.showCart }"
            @click.away="$wire.showCart = false">
            <!-- Sidebar Header -->
            <div class="px-4 py-3 border-b border-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="$wire.showCart = false" class="lg:hidden text-slate-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div
                        class="w-9 h-9 rounded-xl bg-brand-primary/10 flex items-center justify-center text-brand-primary">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-[10px] font-black text-slate-800 uppercase tracking-tight leading-none">Current
                            Order</h2>
                        <span
                            class="text-[8px] text-slate-400 font-bold uppercase tracking-widest mt-0.5 inline-block">{{ count($cart ?? []) }}
                            Items</span>
                    </div>
                </div>
                <button wire:click="clearCart" class="text-rose-400 hover:text-rose-600 transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                </button>
            </div>

            <!-- Scrollable Item List -->
            <div class="flex-1 overflow-y-auto px-4 py-3 space-y-2 min-h-[16vh]">
                @forelse($cart as $index => $item)
                    <div wire:key="cart-item-{{ $item['id'] }}-{{ $index }}"
                        class="flex items-center gap-3 group animate-fadeIn bg-slate-50 rounded-xl px-3 py-2.5 border border-slate-100">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-[10px] font-black text-slate-800 uppercase tracking-tight truncate mb-0.5">
                                {{ $item['name'] }}
                            </h4>
                            <p class="text-brand-primary font-black text-[10px]">৳{{ number_format($item['price']) }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center bg-white border border-slate-200 rounded-lg px-2 py-1">
                                <button wire:click="updateQuantity({{ $index }}, -1)"
                                    class="w-5 h-5 flex items-center justify-center text-slate-400 hover:text-brand-primary transition-colors font-black text-[10px]">-</button>
                                <span
                                    class="w-6 text-center text-[10px] font-black text-slate-800">{{ $item['quantity'] }}</span>
                                <button wire:click="updateQuantity({{ $index }}, 1)"
                                    class="w-5 h-5 flex items-center justify-center text-slate-400 hover:text-brand-primary transition-colors font-black text-[10px]">+</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center py-10 text-center opacity-30">
                        <div class="w-20 h-20 mb-4 bg-slate-50 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <h3 class="text-[10px] font-black text-slate-600 uppercase tracking-widest leading-relaxed">Your
                            basket is waiting...<br><span class="text-[8px] font-bold opacity-60">Click on items to add
                                them</span></h3>
                    </div>
                @endforelse
            </div>

            <!-- Checkout Section -->
            <div
                class="px-4 py-4 bg-slate-50 border-t border-slate-100 rounded-t-[2rem] shadow-[0_-10px_30px_rgba(0,0,0,0.03)] selection:bg-brand-primary selection:text-white">
                <!-- Contextual Fields -->
                <div class="grid grid-cols-2 gap-2.5 mb-2">
                    <div class="col-span-1 space-y-1">
                        <label class="text-[7px] font-black text-slate-400 uppercase tracking-[0.2em] px-1">Customer
                            Name</label>
                        <input wire:model.live="customerName" type="text" placeholder="Walking Customer"
                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-[10px] focus:ring-2 focus:ring-brand-primary/20 outline-none transition-all placeholder:text-slate-300 font-bold">
                    </div>
                    <div class="col-span-1 space-y-1">
                        <label class="text-[7px] font-black text-slate-400 uppercase tracking-[0.2em] px-1">Customer
                            Phone</label>
                        <input wire:model.live="customerPhone" type="text" placeholder="017xxxxxxxx"
                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-[10px] focus:ring-2 focus:ring-brand-primary/20 outline-none transition-all placeholder:text-slate-300 font-bold">
                    </div>
                    <div class="col-span-1 space-y-1">
                        <label class="text-[7px] font-black text-slate-400 uppercase tracking-[0.2em] px-1">Order
                            Type</label>
                        <select wire:model.live="orderType"
                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-[10px] focus:ring-2 focus:ring-brand-primary/20 outline-none transition-all font-bold appearance-none cursor-pointer">
                            <option value="dine_in">WALK-IN</option>
                            <option value="takeaway">TAKEAWAY</option>
                            <option value="delivery">DELIVERY</option>
                        </select>
                    </div>
                    <div class="col-span-1 space-y-1">
                        <label class="text-[7px] font-black text-slate-400 uppercase tracking-[0.2em] px-1">Table /
                            Guest</label>
                        <input wire:model.live="tableNumber" type="text" placeholder="Table No"
                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-[10px] focus:ring-2 focus:ring-brand-primary/20 outline-none transition-all placeholder:text-slate-300 font-bold">
                    </div>
                </div>

                <!-- Manual Order Discount -->
                <div class="space-y-2 mb-2">
                    <div class="flex items-center justify-between px-1">
                        <span
                            class="text-[8px] font-black text-slate-600 uppercase tracking-widest flex items-center gap-1.5">
                            <svg class="w-3 h-3 text-brand-primary" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Discount
                        </span>
                        <div class="flex items-center gap-1 bg-slate-200 p-0.5 rounded-lg">
                            <button wire:click="$set('discountType', 'percentage')"
                                class="px-2 py-0.5 rounded-md text-[8px] font-black transition-all {{ $discountType == 'percentage' ? 'bg-white text-brand-primary shadow-sm' : 'text-slate-500' }}">%</button>
                            <button wire:click="$set('discountType', 'fixed')"
                                class="px-2 py-0.5 rounded-md text-[8px] font-black transition-all {{ $discountType == 'fixed' ? 'bg-white text-brand-primary shadow-sm' : 'text-slate-500' }}">৳</button>
                        </div>
                    </div>
                    <input wire:model.live="discountValue" type="number" placeholder="0.00"
                        class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-[10px] focus:ring-2 focus:ring-brand-primary/20 outline-none transition-all font-bold">
                </div>

                <!-- Totals & Payment -->
                <div class="space-y-4 mb-5">
                    <div class="space-y-2 px-1">
                        <div class="flex justify-between items-center">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Subtotal</span>
                            <span
                                class="text-[10px] font-black text-slate-800">৳{{ number_format($this->subtotal) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-rose-500">
                            <span class="text-[9px] font-bold uppercase tracking-wider">Discount</span>
                            <span class="text-[10px] font-black">-৳{{ number_format($this->discountAmount) }}</span>
                        </div>
                        <div class="pt-3 border-t border-slate-200 flex justify-between items-end">
                            <span class="text-[10px] font-black text-slate-800 uppercase tracking-[0.1em]">Grand
                                Payable</span>
                            <span
                                class="text-xl font-black text-rose-600 drop-shadow-sm">৳{{ number_format($this->total) }}</span>
                        </div>
                    </div>

                    <div class="grid gap-2"
                        style="grid-template-columns: repeat({{ count($enabledPaymentMethods ?? []) }}, minmax(0, 1fr));">
                        @foreach($enabledPaymentMethods as $methodKey => $methodLabel)
                            @php
                                $config = $paymentMethodConfigs[$methodKey] ?? null;
                                if (!$config)
                                    continue;
                                $isOnline = in_array($methodKey, $onlinePaymentMethods);
                                $color = $config['color'];
                            @endphp
                            <button wire:click="$set('paymentMethod', '{{ $methodKey }}')" :class="{
                                        'bg-{{ $color }}-600 text-white shadow-lg shadow-{{ $color }}-500/20': $wire.paymentMethod === '{{ $methodKey }}',
                                        'bg-white text-slate-500 border border-slate-200 hover:bg-slate-50': $wire.paymentMethod !== '{{ $methodKey }}'
                                    }"
                                class="py-3 rounded-xl flex flex-col items-center gap-1 transition-all duration-200 relative">
                                {!! $config['icon'] !!}
                                <span class="text-[7px] font-black uppercase tracking-widest">{{ $methodLabel }}</span>
                            </button>
                        @endforeach
                    </div>

                    <!-- Reference No / Transaction ID - shown for non-cash payments -->
                    <template x-if="$wire.paymentMethod !== 'cash'">
                        <div class="space-y-1 animate-fadeIn">
                            <label
                                class="text-[7px] font-black text-rose-500 uppercase tracking-[0.2em] px-1 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 bg-rose-500 rounded-full"></span>
                                Reference No / Transaction ID (Required)
                            </label>
                            <input wire:model="referenceNo" type="text" placeholder="e.g. TRX-123456"
                                class="w-full bg-white border border-rose-100 rounded-xl px-3 py-2 text-[10px] focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none transition-all font-bold">
                        </div>
                    </template>
                </div>

                <div class="flex gap-2">
                    <button @click="if(!isOnline) {
                        await saveOfflineOrder($wire.cart, $wire.total, {
                            order_type: $wire.orderType,
                            payment_method: $wire.paymentMethod,
                            table_number: $wire.tableNumber,
                            customer_name: $wire.customerName,
                            reference_no: $wire.referenceNo,
                            notes: $wire.notes
                        });
                        $wire.cart = [];
                        $wire.customerName = '';
                        $wire.customerPhone = '';
                        $wire.tableNumber = '';
                        $wire.notes = '';
                        $wire.referenceNo = '';
                        $wire.discountValue = 0;
                        window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'success', message: 'Offline Order Saved! It will sync once online.' } }));

                    } else {
                        $wire.placeOrder()
                    }" {{ empty($cart) ? 'disabled' : '' }}
                        class="flex-1 py-3.5 bg-brand-primary text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-xl shadow-2xl shadow-brand-primary/40 hover:bg-brand-primary-dark transition-all disabled:opacity-20 flex items-center justify-center gap-2.5 group active:scale-[0.98]">
                        <svg class="w-4 h-4 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Confirm
                    </button>

                    {{-- Manual Print button - only shown when a receipt is available --}}
                    <button @click="window.printPosReceipt(lastReceipt)" x-show="lastReceipt !== null" x-cloak
                        title="Print Last Receipt"
                        class="w-12 h-full py-3.5 bg-slate-700 text-white rounded-xl shadow-lg hover:bg-slate-800 transition-all flex items-center justify-center active:scale-[0.98] flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Order Success Modal with Products Info -->
        <template x-if="showSuccessModal">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="showSuccessModal = false">
                </div>

                <!-- Modal Content -->
                <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg relative z-10 overflow-hidden flex flex-col max-h-[90vh]"
                     x-transition:enter="ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                     @click.stop>
                     
                    <!-- Header -->
                    <div class="bg-brand-primary p-6 text-center relative overflow-hidden shrink-0">
                        <div class="absolute inset-0 bg-white/10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px; opacity: 0.2;"></div>
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-[0_0_30px_rgba(255,255,255,0.3)] relative z-10">
                            <svg class="w-8 h-8 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h2 class="text-white text-xl font-black uppercase tracking-wider relative z-10">Order Placed Successfully!</h2>
                        <p class="text-white/80 text-xs font-bold mt-1 relative z-10" x-text="'Order #' + lastReceipt?.order_number"></p>
                    </div>

                    <!-- Products List -->
                    <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Ordered Items
                        </h3>
                        
                        <div class="space-y-3">
                            <template x-for="(item, index) in lastReceipt?.items" :key="index">
                                <div class="flex items-center gap-4 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center shrink-0 border border-slate-200">
                                        <span class="text-brand-primary font-black text-sm" x-text="item.qty + 'x'"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-tight truncate" x-text="item.name"></h4>
                                        <p class="text-[10px] text-slate-500 font-bold mt-0.5" x-text="'৳' + item.price + ' each'"></p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-brand-primary font-black text-sm" x-text="'৳' + item.subtotal"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Order Summary -->
                        <div class="mt-6 pt-4 border-t border-slate-100 space-y-2">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-500 font-bold uppercase">Subtotal</span>
                                <span class="text-slate-800 font-black" x-text="'৳' + lastReceipt?.subtotal"></span>
                            </div>
                            <template x-if="lastReceipt?.discount > 0">
                                <div class="flex justify-between items-center text-xs text-rose-500">
                                    <span class="font-bold uppercase">Discount</span>
                                    <span class="font-black" x-text="'-৳' + lastReceipt?.discount"></span>
                                </div>
                            </template>
                            <div class="flex justify-between items-end pt-2 mt-2 border-t border-slate-100">
                                <span class="text-slate-800 font-black uppercase text-xs">Total Paid</span>
                                <span class="text-brand-primary font-black text-xl" x-text="'৳' + lastReceipt?.total"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex gap-3 shrink-0">
                        <button @click="if (lastReceipt) window.printPosReceipt(lastReceipt)" 
                                class="flex-1 py-3 bg-white text-slate-700 border border-slate-200 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-slate-50 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 00-2-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print Receipt
                        </button>
                        <button @click="showSuccessModal = false" 
                                class="flex-1 py-3 bg-brand-primary text-white shadow-lg shadow-brand-primary/30 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-brand-primary-dark transition-colors flex items-center justify-center gap-2">
                            New Order
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Global Notifications now handled by x-layout -->


        <style>
            [x-cloak] {
                display: none !important;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fadeIn {
                animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
            }

            .custom-scrollbar::-webkit-scrollbar {
                width: 5px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(0, 0, 0, 0.04);
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(0, 0, 0, 0.1);
            }

            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }

            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        </style>
        <x-notifications />

        @script
        <script>
            // Listen for print-receipt event dispatched by PosSystem::placeOrder()
            $wire.on('print-receipt', ({ receipt }) => {
                // Update Alpine lastReceipt state via a bridge window event
                window.dispatchEvent(new CustomEvent('pos-receipt-ready', { detail: receipt }));

                if (receipt.auto_print) {
                    window.printPosReceipt(receipt);
                }
            });
        </script>
        @endscript
    </div>
</div>