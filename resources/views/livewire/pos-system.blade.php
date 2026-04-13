<div>
    <!-- CSS Loading Screen (Prevents FOUC from Vite in dev mode) -->
    <div class="!hidden" style="position: fixed; inset: 0; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); z-index: 99999; display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <svg style="width: 48px; height: 48px; color: #808000; animation: pos-spin 1s linear infinite;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p style="margin-top: 16px; font-family: 'Outfit', sans-serif; font-weight: 900; color: #808000; font-size: 12px; letter-spacing: 0.2em; text-transform: uppercase;">Booting POS...</p>
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
                isProcessing: false,
                isReady: false,
                isDarkMode: false,
                init() {
                    this.isDarkMode = document.documentElement.classList.contains('dark') || window.matchMedia('(prefers-color-scheme: dark)').matches;

                    const darkClassObserver = new MutationObserver(() => {
                        this.isDarkMode = document.documentElement.classList.contains('dark');
                    });

                    darkClassObserver.observe(document.documentElement, {
                        attributes: true,
                        attributeFilter: ['class']
                    });

                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
                        if (!document.documentElement.classList.contains('dark')) {
                            this.isDarkMode = event.matches;
                        }
                    });
                    this.db = new Dexie('POS_Offline_DB');
                    this.db.version(2).stores({
                        orders: '++id, cart, total, timestamp, details, synced'
                    });

                    // Keyboard Shortcuts
                    window.addEventListener('keydown', (e) => {
                        // Focus search on '/'
                        if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                            e.preventDefault();
                            document.querySelector('.pos-search-input')?.focus();
                        }

                        // Close modal on 'Escape'
                        if (e.key === 'Escape') {
                            this.showSuccessModal = false;
                        }

                        // Confirm order on 'Enter' (if cart is not empty and modal is not open)
                        if (e.key === 'Enter' && !this.showSuccessModal && !this.isProcessing) {
                            const cartCount = @json(count($cart));
                            if (cartCount > 0 && document.activeElement.tagName !== 'TEXTAREA' && document.activeElement.tagName !== 'INPUT') {
                                e.preventDefault();
                                this.placeOrderStandard();
                            }
                        }

                        // Select Payment Methods with F1-F4
                        if (e.key === 'F1') { e.preventDefault(); $wire.set('paymentMethod', 'cash'); }
                        if (e.key === 'F2') { e.preventDefault(); $wire.set('paymentMethod', 'card'); }
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

                    this.$nextTick(() => {
                        this.isReady = true;
                    });
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
                },
                async placeOrderStandard() {
                    const cartCount = this.$wire.cart ? Object.keys(this.$wire.cart).length : 0;
                    if (cartCount <= 0) return;

                    this.isProcessing = true;
                    if (!this.isOnline) {
                        try {
                            await this.saveOfflineOrder(this.$wire.cart, this.$wire.total, {
                                order_type: this.$wire.orderType,
                                payment_method: this.$wire.paymentMethod,
                                table_number: this.$wire.tableNumber,
                                customer_name: this.$wire.customerName,
                                reference_no: this.$wire.referenceNo,
                                notes: this.$wire.notes
                            });
                            this.$wire.cart = [];
                            this.$wire.customerName = '';
                            this.$wire.customerPhone = '';
                            this.$wire.tableNumber = '';
                            this.$wire.referenceNo = '';
                            this.$wire.notes = '';
                            this.$wire.discountValue = 0;

                            window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'success', message: 'Offline Order Saved! It will sync once online.' } }));
                        } finally {
                            this.isProcessing = false;
                        }
                    } else {
                        try {
                            await this.$wire.placeOrder();
                        } finally {
                            this.isProcessing = false;
                        }
                    }
                }
            };
        }
    </script>

        <div x-data="posSystemData()"
        x-on:order-placed.window="if (!isOnline) { await saveOfflineOrder($wire.cart, $wire.total, { order_number: $event.detail?.order_number, order_type: $wire.orderType, payment_method: $wire.paymentMethod, table_number: $wire.tableNumber, customer_name: $wire.customerName, customer_phone: $wire.customerPhone, guest_count: $wire.guestCount, notes: $wire.notes, reference_no: $wire.referenceNo }); } else { syncOrders(); }"
        x-on:pos-receipt-ready.window="lastReceipt = $event.detail; showSuccessModal = !$event.detail.auto_print"
        x-cloak
        x-show="isReady"
        style="display: none;"
        class="!flex flex h-full overflow-hidden font-sans relative pos-bg"
        :class="{ 'overflow-hidden': $wire.showCart, 'pos-dark': isDarkMode }"
>

        <!-- Offline Sync Status Indicator -->
        <div class="fixed top-4 right-4 z-50">
            @livewire('offline-sync-status')
        </div>

        <!-- Mobile Cart Toggle - Floating Action Button -->
        <button @click="$wire.showCart = true"
            class="lg:hidden fixed bottom-6 right-6 z-50 w-16 h-16 rounded-2xl shadow-2xl flex items-center justify-center pos-fab-cart group"
            style="background: linear-gradient(135deg, #808000 0%, #a4a400 100%); box-shadow: 0 8px 32px rgba(128,128,0,0.45);">
            <div class="relative">
                <svg class="w-7 h-7 text-white group-hover:scale-110 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <template x-if="$wire.cart.length > 0">
                    <span
                        class="absolute -top-2.5 -right-2.5 bg-rose-500 text-white text-[9px] font-black w-5 h-5 rounded-full flex items-center justify-center border-2 border-white shadow-lg"
                        x-text="$wire.cart.length"></span>
                </template>
            </div>
        </button>

        <!-- ═══════════════════ MAIN PANEL ═══════════════════ -->
        <main class="flex-1 flex flex-col h-[100vh] min-w-0 overflow-hidden">

            <!-- ── Top Header Bar ── -->
            <div class="pos-header px-4 md:px-6 py-3 flex flex-col md:flex-row items-center gap-3 shrink-0">
                <!-- Brand Mark -->
                <div class="hidden md:flex items-center gap-3 shrink-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #808000, #a4a400);">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-slate-400">Point of Sale</p>
                        <p class="text-[11px] font-black text-slate-700 -mt-0.5">Quick Order</p>
                    </div>
                    <div class="w-px h-8 bg-slate-200 mx-1"></div>
                </div>

                <!-- Search -->
                <div class="w-full md:flex-1 relative group">
                    <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-primary transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search menu items…"
                        class="w-full pos-search-input py-2.5 pl-10 pr-4 text-[11px] transition-all duration-200">
                </div>

                <!-- Status Badges -->
                <div class="flex items-center gap-3 shrink-0">
                    <div x-show="!isOnline"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-[9px] font-bold uppercase tracking-wider animate-pulse"
                        style="background: #fef2f2; color: #dc2626; border-color: #fecaca;">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Offline
                    </div>
                    <div x-show="syncing"
                        class="flex items-center gap-1.5 text-brand-primary text-[9px] font-bold uppercase tracking-wider">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Syncing…
                    </div>
                    <!-- Item count stat -->
                    <div class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[9px] font-bold uppercase tracking-wider pos-stat-badge">
                        <svg class="w-3.5 h-3.5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        {{ $items->count() }} / {{ $totalItems }} Items
                    </div>
                </div>
            </div>

            <!-- ── Category Filter Pills ── -->
            <div class="px-1 md:px-2 py-1 flex items-center gap-1 shrink-0">
                <button
                    type="button"
                    @click="$refs.categoryScroller.scrollBy({ left: -220, behavior: 'smooth' })"
                    class="w-6 h-6 md:w-7 md:h-7 rounded-full flex items-center justify-center border border-slate-200 bg-white/90 text-slate-500 hover:text-brand-primary hover:border-brand-primary transition-all duration-200 shadow-sm"
                    aria-label="Scroll categories left">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <div x-ref="categoryScroller" class="pos-categories flex-1 px-0.5 py-0 flex items-center gap-1 overflow-x-auto scrollbar-hide">
                    <button wire:click="selectCategory(null)"
                        class="pos-category-pill flex-shrink-0 text-[9px] px-2 py-1 {{ !$selectedCategoryId ? 'active' : '' }}">
                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        All
                    </button>
                    @foreach($categories as $category)
                        <button wire:key="category-{{ $category->id }}" wire:click="selectCategory({{ $category->id }})"
                            class="pos-category-pill flex-shrink-0 text-[9px] px-2 py-1 {{ $selectedCategoryId == $category->id ? 'active' : '' }}">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>

                <button
                    type="button"
                    @click="$refs.categoryScroller.scrollBy({ left: 220, behavior: 'smooth' })"
                    class="w-6 h-6 md:w-7 md:h-7 rounded-full flex items-center justify-center border border-slate-200 bg-white/90 text-slate-500 hover:text-brand-primary hover:border-brand-primary transition-all duration-200 shadow-sm"
                    aria-label="Scroll categories right">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar">
                <!-- ── Most Ordered / Fast Moving Items (Only shown on "All" category and no search) ── -->
                @if(!$selectedCategoryId && !$search && $frequentItems->isNotEmpty())
                <div class="px-4 md:px-6 pt-2">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-[0.15em] flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Most Ordered
                        </h3>
                    </div>
                    <div class="flex gap-1.5 overflow-x-auto pb-2 scrollbar-hide">
                        @foreach($frequentItems as $item)
                            <div wire:key="frequent-{{ $item->id }}" wire:click="addToCart({{ $item->id }})"
                                 class="flex-shrink-0 w-20 md:w-22 group cursor-pointer">
                                <div class="relative aspect-square rounded-md overflow-hidden mb-0.5 shadow-sm group-hover:shadow-md transition-all duration-300">
                                    <img src="{{ $item->image ? Storage::url($item->image) : asset('placeholder.png') }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                    <div class="absolute bottom-1 left-1">
                                        <span class="text-[7px] font-black text-white">৳{{ number_format($item->final_price) }}</span>
                                    </div>
                                    @if($item->available_stock <= 0)
                                        <div class="absolute inset-0 bg-slate-900/60 flex items-center justify-center">
                                            <span class="text-[7px] text-white font-black uppercase">Sold Out</span>
                                        </div>
                                    @endif
                                </div>
                                <h4 class="text-[6px] font-bold text-slate-700 truncate text-center group-hover:text-brand-primary uppercase tracking-tight">{{ $item->name }}</h4>
                            </div>
                        @endforeach
                    </div>
                    <div class="h-px bg-slate-100 mt-2"></div>
                </div>
                @endif

                <!-- ── Menu Grid ── -->
                <div class="p-4 md:p-5 min-h-0">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2.5 md:gap-3">
                    @foreach($items as $item)
                        <div wire:key="item-{{ $item->id }}" wire:click="addToCart({{ $item->id }})"
                            class="pos-menu-card group flex flex-col h-full relative cursor-pointer {{ $item->available_stock <= 0 ? 'opacity-75 cursor-not-allowed' : '' }}">

                            <!-- Image Container -->
                            <div class="relative aspect-[4/3] overflow-hidden rounded-lg mb-2">
                                <img src="{{ $item->image ? Storage::url($item->image) : asset('placeholder.png') }}"
                                    alt="{{ $item->name }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out">

                                <!-- Gradient overlay bottom -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>

                                <!-- Price chip -->
                                <div class="absolute bottom-2 left-2">
                                    <span class="pos-price-chip">৳{{ number_format($item->final_price) }}</span>
                                </div>

                                <!-- Stock badge top right -->
                                @if($item->available_stock > 0 && $item->available_stock < 5)
                                    <div class="absolute top-2 right-2">
                                        <span class="pos-low-stock-badge">{{ $item->available_stock }} left</span>
                                    </div>
                                @endif

                                <!-- Out of Stock Overlay -->
                                @if($item->available_stock <= 0)
                                    <div class="absolute inset-0 bg-slate-900/55 backdrop-blur-[1px] flex items-center justify-center rounded-xl">
                                        <span class="bg-rose-600 text-white text-[8px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full shadow-xl">
                                            Out of Stock
                                        </span>
                                    </div>
                                @endif

                                <!-- Add to Cart hover overlay -->
                                @if($item->available_stock > 0)
                                    <div class="absolute inset-0 bg-brand-primary/0 group-hover:bg-brand-primary/15 transition-all duration-300 rounded-xl flex items-center justify-center">
                                        <div class="w-8 h-8 bg-white rounded-full shadow-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-300">
                                            <svg class="w-3.5 h-3.5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="flex-1 px-0.5 pb-0.5">
                                <h3 class="text-[9px] font-black text-slate-800 uppercase tracking-tight mb-0.5 group-hover:text-brand-primary transition-colors duration-200 line-clamp-1">
                                    {{ $item->name }}
                                </h3>
                                <p class="text-[8px] text-slate-400 font-semibold italic leading-none">{{ $item->category->name }}</p>

                                <div class="mt-1.5 flex items-center gap-1.5 flex-wrap">
                                    @if($item->isPremade())
                                        <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 text-[7px] font-black uppercase tracking-wide px-1.5 py-0.5 border border-amber-200">
                                            Premade
                                        </span>
                                    @endif

                                    @if($item->available_stock > 0 && $item->available_stock >= 5)
                                        <span class="inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span class="text-[7px] font-bold text-slate-400 uppercase tracking-wide">In Stock</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Load More --}}
                @if($hasMoreItems)
                    <div class="col-span-full pt-2 pb-1 flex justify-center">
                        <button
                            wire:click="loadMore"
                            wire:loading.attr="disabled"
                            wire:target="loadMore"
                            class="flex items-center gap-2 px-6 py-2.5 rounded-xl border-2 text-[9px] font-black uppercase tracking-widest transition-all duration-200 active:scale-[0.97] disabled:opacity-50 disabled:cursor-wait"
                            style="border-color: #808000; color: #808000;"
                            onmouseover="this.style.background='#808000'; this.style.color='white';"
                            onmouseout="this.style.background='transparent'; this.style.color='#808000';">
                            <svg wire:loading.remove wire:target="loadMore" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                            <svg wire:loading wire:target="loadMore" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span wire:loading.remove wire:target="loadMore">Load More — {{ $totalItems - $items->count() }} more</span>
                            <span wire:loading wire:target="loadMore">Loading…</span>
                        </button>
                    </div>
                @endif

                <!-- Empty State -->
                @if($items->isEmpty())
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <div class="w-20 h-20 rounded-2xl pos-empty-icon-bg flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <p class="text-[11px] font-black text-slate-500 uppercase tracking-widest">No items found</p>
                        <p class="text-[9px] text-slate-400 mt-1">Try a different search or category</p>
                    </div>
                @endif
                </div>
            </div>
        </main>

        <!-- ═══════════════════ ORDER SIDEBAR ═══════════════════ -->
        <aside
            class="fixed inset-y-0 right-0 w-full md:w-[360px] xl:w-[380px] h-[100dvh] max-h-[100dvh] overflow-hidden flex flex-col z-50 transform transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] lg:relative lg:translate-x-0 pos-sidebar"
            :class="{ 'translate-x-0': $wire.showCart, 'translate-x-full': !$wire.showCart }"
            @click.away="$wire.showCart = false">

            <!-- ── Sidebar Header ── -->
            <div class="px-4 py-3 flex items-center justify-between shrink-0 pos-sidebar-header">
                <div class="flex items-center gap-2.5">
                    <button @click="$wire.showCart = false" class="lg:hidden w-7 h-7 rounded-lg flex items-center justify-center text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #808000 0%, #a4a400 100%); box-shadow: 0 3px 10px rgba(128,128,0,0.25);">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-[7px] font-black text-slate-800 uppercase tracking-tight leading-none">Current Order</h2>
                        <span class="text-[7px] text-slate-400 font-bold mt-0.5 inline-flex items-center gap-1">
                            <span class="w-1 h-1 rounded-full {{ count($cart ?? []) > 0 ? 'bg-brand-primary' : 'bg-slate-300' }}"></span>
                            {{ count($cart ?? []) }} {{ count($cart ?? []) === 1 ? 'item' : 'items' }}
                        </span>
                    </div>
                </div>
                <button wire:click="clearCart"
                    class="group w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-200 {{ empty($cart) ? 'text-slate-300 cursor-not-allowed' : 'text-rose-400 hover:text-rose-600 hover:bg-rose-50' }}"
                    {{ empty($cart) ? 'disabled' : '' }}
                    title="Clear cart">
                    <svg class="w-3.5 h-3.5 group-hover:scale-110 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>

            <!-- ── Cart Items ── -->
            <div class="flex-1 overflow-y-auto px-3 md:px-4 py-2.5 space-y-1.5 min-h-[20vh] max-h-[38vh] md:max-h-[40vh] custom-scrollbar">
                @forelse($cart as $index => $item)
                    <div wire:key="cart-item-{{ $item['id'] }}-{{ $index }}"
                        class="pos-cart-item flex items-center gap-3 animate-fadeIn">
                        <!-- Qty Badge -->
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background: linear-gradient(135deg, #808000, #a4a400);">
                            <span class="text-white font-black text-[10px]">{{ $item['quantity'] }}</span>
                        </div>

                        <!-- Item Info -->
                        <div class="flex-1 min-w-0">
                            <h4 class="text-[10px] font-black text-slate-800 uppercase tracking-tight truncate">{{ $item['name'] }}</h4>
                            <p class="text-brand-primary font-black text-[10px] mt-0.5">৳{{ number_format($item['price'] * $item['quantity']) }}</p>
                        </div>

                        <!-- Qty Controls + Remove -->
                        <div class="flex items-center gap-1">
                            <button wire:click="removeFromCart({{ $index }})"
                                class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all duration-150"
                                title="Remove item">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>

                            <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-0.5">
                            <button wire:click="updateQuantity({{ $index }}, -1)"
                                class="w-6 h-6 rounded-md flex items-center justify-center text-slate-500 hover:text-rose-500 hover:bg-white transition-all duration-150 font-black text-sm">
                                −
                            </button>
                            <span class="w-5 text-center text-[10px] font-black text-slate-700">{{ $item['quantity'] }}</span>
                            <button wire:click="updateQuantity({{ $index }}, 1)"
                                class="w-6 h-6 rounded-md flex items-center justify-center text-slate-500 hover:text-brand-primary hover:bg-white transition-all duration-150 font-black text-sm">
                                +
                            </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-20 h-20 mb-4 rounded-2xl pos-empty-cart-icon flex items-center justify-center">
                            <svg class="w-9 h-9 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Basket is empty</h3>
                        <p class="text-[8px] text-slate-400 font-bold mt-1">Tap menu items to add them</p>
                    </div>
                @endforelse
            </div>

            <!-- ── Checkout Panel ── -->
            <div class="shrink min-h-0 lg:shrink-0 pos-checkout-panel px-3 md:px-4 pt-3 pb-2 space-y-2.5 flex flex-col">

                <div class="flex-1 min-h-0 space-y-2.5 pr-0.5 pb-24 lg:pb-2 overflow-y-auto custom-scrollbar">

                <!-- Divider with label -->
                <div class="flex items-center gap-2 mb-1">
                    <div class="flex-1 h-px bg-slate-200"></div>
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.15em]">Order Details</span>
                    <div class="flex-1 h-px bg-slate-200"></div>
                </div>

                <div>

                <!-- Customer Fields Grid -->
                <div x-data="{ showCustomerFields: window.innerWidth >= 768 }" class="space-y-2">
                    <button type="button"
                        class="md:hidden w-full flex items-center justify-between px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-[9px] font-black uppercase tracking-[0.12em] text-slate-600"
                        @click="showCustomerFields = !showCustomerFields">
                        <span>Customer & Order Details</span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': showCustomerFields }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="showCustomerFields || window.innerWidth >= 768" x-collapse class="grid grid-cols-2 gap-1.5 md:gap-2">
                    <div class="space-y-1">
                        <label class="pos-field-label">Customer Name</label>
                        <input wire:model.live="customerName" type="text" placeholder="Walk-in Customer"
                            class="pos-field-input">
                    </div>
                    <div class="space-y-1">
                        <label class="pos-field-label">Phone</label>
                        <input wire:model.live="customerPhone" type="text" placeholder="017xxxxxxxx"
                            class="pos-field-input">
                    </div>
                    <div class="col-span-2 grid grid-cols-3 gap-1.5 md:gap-2">
                        <div class="space-y-1">
                            <label class="pos-field-label">Order Type</label>
                            <select wire:model.live="orderType" class="pos-field-select">
                                <option value="dine_in">🍽 Walk-In</option>
                                <option value="takeaway">🛍 Takeaway</option>
                                <option value="delivery">🚴 Delivery</option>
                                <option value="foodpanda">🐼 Foodpanda</option>
                                <option value="pathao">🚗 Pathao</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="pos-field-label">Guests</label>
                            <input wire:model.live="guestCount" type="number" min="1" placeholder="1"
                                class="pos-field-input">
                        </div>
                        <div class="space-y-1">
                            <label class="pos-field-label">Table (Optional)</label>
                            <select wire:model.live="tableNumber" class="pos-field-select">
                                <option value="">No Table</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->name }}">{{ $table->name }} ({{ $table->capacity }} seats)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    </div>
                </div>

                <!-- Old Visual Table Selector Removed to follow "select option" request -->

                <!-- Discount Row -->
                <div class="pos-discount-row rounded-xl p-2 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1 text-[8px] font-black text-slate-600 uppercase tracking-wider">
                            <svg class="w-3 h-3 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Discount
                        </span>
                        <div class="flex items-center gap-0.5 bg-white rounded-lg p-0.5 border border-slate-200">
                            <button wire:click="$set('discountType', 'percentage')"
                                class="px-2 py-0.5 rounded-md text-[8px] font-black transition-all duration-200 {{ $discountType == 'percentage' ? 'bg-brand-primary text-white shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">%</button>
                            <button wire:click="$set('discountType', 'fixed')"
                                class="px-2 py-0.5 rounded-md text-[8px] font-black transition-all duration-200 {{ $discountType == 'fixed' ? 'bg-brand-primary text-white shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">৳</button>
                        </div>
                    </div>
                    <input wire:model.live="discountValue" type="number" placeholder="0.00"
                        class="pos-field-input !py-1.5 text-center font-black text-[10px]">
                </div>

                </div>

                <!-- Totals -->
                <div class="pos-totals-card rounded-xl p-2.5 md:p-3 space-y-1.5 sticky bottom-[4.65rem] md:bottom-[4.95rem] z-20 bg-white/95 backdrop-blur-sm border border-slate-200">
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Subtotal</span>
                        <span class="text-[10px] font-black text-slate-700">৳{{ number_format($this->subtotal) }}</span>
                    </div>
                    @if($this->discountAmount > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-rose-500 uppercase tracking-wider">Discount</span>
                        <span class="text-[10px] font-black text-rose-500">−৳{{ number_format($this->discountAmount) }}</span>
                    </div>
                    @endif
                    <div class="pt-2 border-t border-slate-200 flex justify-between items-end">
                        <span class="text-[9px] font-black text-slate-700 uppercase tracking-wider">Grand Total</span>
                        <span class="text-2xl font-black" style="color: #808000; line-height: 1;">৳{{ number_format($this->total) }}</span>
                    </div>
                </div>

                <!-- Partial Payment Toggle -->
                <div class="flex items-center justify-between bg-gradient-to-r from-slate-50 to-white px-2.5 py-1.5 rounded-xl border border-slate-200/90 shadow-sm">
                    <span class="text-[8px] font-black text-slate-600 uppercase tracking-[0.2em] flex items-center gap-1.5">
                         <svg class="w-3 h-3 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Partial Payment
                    </span>
                    <button wire:click="$toggle('isSplitPayment')"
                        class="relative inline-flex h-4.5 w-9 shrink-0 cursor-pointer rounded-full border border-slate-300/80 transition-all duration-200 ease-in-out focus:outline-none {{ $isSplitPayment ? 'bg-brand-primary/90' : 'bg-slate-200' }}">
                        <span class="pointer-events-none inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $isSplitPayment ? 'translate-x-5' : 'translate-x-0.5' }}"></span>
                    </button>
                </div>

                <!-- Partial Payment Rows -->
                @if($isSplitPayment)
                    <div class="space-y-2 max-h-[140px] md:max-h-[180px] overflow-y-auto pr-1 animate-fadeIn">
                        @foreach($paymentSplits as $index => $split)
                            <div class="bg-white border border-slate-200 rounded-xl p-2 space-y-2 relative group shadow-sm">
                                <div class="flex items-center gap-2">
                                    <select wire:model="paymentSplits.{{ $index }}.method" class="pos-field-select !py-1 text-[9px] flex-1">
                                        @foreach($paymentMethodConfigs as $key => $config)
                                            <option value="{{ $key }}">{{ $config['label'] }}</option>
                                        @endforeach
                                    </select>
                                    <input wire:model="paymentSplits.{{ $index }}.amount" type="number" step="0.01" placeholder="Amount"
                                        class="pos-field-input !py-1 text-[9px] w-24 text-right font-black">
                                    <button wire:click="removePaymentSplit({{ $index }})" class="p-1 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                                <input wire:model="paymentSplits.{{ $index }}.reference_no" type="text" placeholder="Ref No (Optional)"
                                    class="pos-field-input !py-1 text-[8px] italic">
                            </div>
                        @endforeach
                        <button wire:click="addPaymentSplit" class="w-full py-1.5 border-2 border-dashed border-slate-300 rounded-xl text-[8px] font-black text-slate-500 uppercase tracking-widest hover:border-brand-primary hover:text-brand-primary transition-all flex items-center justify-center gap-2">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            Add Payment Method
                        </button>
                    </div>
                @else
                    <!-- Payment Methods -->
                    <div class="grid grid-cols-3 lg:grid-cols-3 gap-1 md:gap-1.5">
                        @foreach($enabledPaymentMethods as $methodKey => $methodLabel)
                            @php
                                $config = $paymentMethodConfigs[$methodKey] ?? null;
                                if (!$config) continue;
                                $isOnline = in_array($methodKey, $onlinePaymentMethods);
                                $color = $config['color'];
                            @endphp
                            <button wire:click="$set('paymentMethod', '{{ $methodKey }}')" :class="{
                                        'ring-2 ring-offset-1 ring-{{ $color }}-500 bg-gradient-to-b from-white to-slate-50 text-slate-600 shadow-md scale-[1.01]': $wire.paymentMethod === '{{ $methodKey }}',
                                        'bg-white/95 text-slate-500 border border-slate-200 hover:border-{{ $color }}-300 hover:bg-{{ $color }}-50/60': $wire.paymentMethod !== '{{ $methodKey }}'
                                    }"
                                class="py-1.5 rounded-lg flex flex-col items-center gap-0.5 transition-all duration-200 relative">
                                {!! $config['icon'] !!}
                                <span class="text-[6px] font-black uppercase tracking-[0.16em] text-slate-600">{{ $methodLabel }}</span>
                            </button>
                        @endforeach
                    </div>

                    <!-- Reference No / Transaction ID -->
                    <div class="space-y-1 animate-fadeIn"
                         x-show="$wire.paymentMethod !== 'cash'"
                         x-cloak
                         style="{{ $paymentMethod !== 'cash' ? '' : 'display:none' }}">
                        <label class="pos-field-label text-rose-500 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 bg-rose-500 rounded-full inline-block"></span>
                            Reference / Transaction ID (Required)
                        </label>
                        <input wire:model="referenceNo" type="text" placeholder="e.g. TRX-123456"
                            class="pos-field-input border-rose-200 focus:border-rose-400 focus:ring-rose-500/20">
                    </div>
                @endif

                </div>

                <!-- Action Buttons -->
                <div class="fixed lg:static bottom-0 left-0 right-0 md:left-auto md:w-[360px] xl:w-[380px] shrink-0 z-40 bg-white/95 backdrop-blur-sm border-t border-slate-200 pt-2 pb-[calc(env(safe-area-inset-bottom)+0.5rem)] px-3 md:px-4 flex gap-2 mt-1">
                    <button @click="placeOrderStandard()"
                        {{ empty($cart) ? 'disabled' : '' }}
                        class="flex-1 py-2.5 text-white text-[9px] font-black uppercase tracking-[0.2em] rounded-lg transition-all duration-200 disabled:opacity-30 flex items-center justify-center gap-2 group active:scale-[0.98]"
                        style="{{ !empty($cart) ? 'background: linear-gradient(135deg, #0f766e 0%, #0ea5a4 100%); box-shadow: 0 6px 18px rgba(15,118,110,0.35);' : 'background: #cbd5e1;' }}">
                        <svg class="w-3.5 h-3.5 group-hover:rotate-12 transition-transform duration-300" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Place Order [Enter]
                    </button>

                    {{-- Manual Print button --}}
                    <button @click="window.printPosReceipt(lastReceipt)" x-show="lastReceipt !== null" x-cloak
                        title="Print Last Receipt"
                        class="w-12 py-3.5 bg-slate-700 text-white rounded-xl shadow-lg hover:bg-slate-800 transition-all duration-200 flex items-center justify-center active:scale-[0.98] shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                    </button>
                </div>

                {{-- Send to Kitchen Button (appears after order is placed) --}}
                @if(isset($lastOrderId) && $lastOrderId)
                @php
                    $orderHasKot = \App\Models\Order::find($lastOrderId)?->hasKot() ?? false;
                @endphp
                @if(!$orderHasKot)
                <div class="px-3 md:px-4 mt-2">
                    <button wire:click="sendToKitchen({{ $lastOrderId }})"
                        class="w-full py-2 bg-orange-500 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-lg hover:bg-orange-600 transition-all flex items-center justify-center gap-2 active:scale-[0.98]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                        </svg>
                        Send to Kitchen
                    </button>
                </div>
                @else
                <div class="px-3 md:px-4 mt-2">
                    <div class="w-full py-2 bg-green-100 text-green-700 text-xs font-bold uppercase tracking-wider rounded-lg text-center">
                        ✓ Sent to Kitchen
                    </div>
                </div>
                @endif
                @endif
            </div>
        </aside>

        <!-- ═══════════════════ SUCCESS MODAL ═══════════════════ -->
        <template x-if="showSuccessModal">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-md"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="showSuccessModal = false">
                </div>

                <!-- Modal -->
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg relative z-10 overflow-hidden flex flex-col max-h-[90vh]"
                     x-transition:enter="ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                     @click.stop>

                    <!-- Modal Header -->
                    <div class="pos-modal-header p-6 text-center relative overflow-hidden shrink-0">
                        <!-- Dot grid decoration -->
                        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.25) 1px, transparent 0); background-size: 20px 20px;"></div>
                        <!-- Glow circles -->
                        <div class="absolute -top-8 -left-8 w-32 h-32 rounded-full" style="background: rgba(255,255,255,0.08);"></div>
                        <div class="absolute -bottom-8 -right-8 w-32 h-32 rounded-full" style="background: rgba(255,255,255,0.08);"></div>

                        <div class="relative z-10">
                            <!-- Success icon -->
                            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-xl">
                                <svg class="w-8 h-8 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h2 class="text-white text-lg font-black uppercase tracking-wider">Order Placed!</h2>
                            <p class="text-white/80 text-[10px] font-bold mt-1 uppercase tracking-widest" x-text="'Order #' + lastReceipt?.order_number"></p>
                        </div>
                    </div>

                    <!-- Modal Body – Ordered Items -->
                    <div class="p-5 overflow-y-auto flex-1 custom-scrollbar">
                        <h3 class="text-[10px] font-black text-slate-700 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Order Summary
                        </h3>

                        <div class="space-y-2">
                            <template x-for="(item, index) in lastReceipt?.items" :key="index">
                                <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background: linear-gradient(135deg, #808000, #a4a400);">
                                        <span class="text-white font-black text-[10px]" x-text="item.qty + 'x'"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-[10px] font-black text-slate-800 uppercase tracking-tight truncate" x-text="item.name"></h4>
                                        <p class="text-[9px] text-slate-400 font-bold mt-0.5" x-text="'৳' + item.price + ' each'"></p>
                                    </div>
                                    <span class="text-brand-primary font-black text-sm shrink-0" x-text="'৳' + item.subtotal"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Totals summary -->
                        <div class="mt-4 pt-4 border-t border-slate-100 space-y-2">
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="text-slate-500 font-bold uppercase">Subtotal</span>
                                <span class="text-slate-800 font-black" x-text="'৳' + lastReceipt?.subtotal"></span>
                            </div>
                            <template x-if="lastReceipt?.discount > 0">
                                <div class="flex justify-between items-center text-[10px] text-rose-500">
                                    <span class="font-bold uppercase">Discount</span>
                                    <span class="font-black" x-text="'-৳' + lastReceipt?.discount"></span>
                                </div>
                            </template>
                            <div class="flex justify-between items-end pt-2 mt-1 border-t border-slate-100">
                                <span class="text-slate-700 font-black uppercase text-[10px]">Total Paid</span>
                                <span class="font-black text-xl" style="color: #808000;" x-text="'৳' + lastReceipt?.total"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer Actions -->
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex gap-3 shrink-0">
                        <button @click="if (lastReceipt) window.printPosReceipt(lastReceipt)"
                                class="flex-1 py-3 bg-white text-slate-700 border border-slate-200 font-black text-[9px] uppercase tracking-widest rounded-xl hover:bg-slate-50 transition-colors duration-200 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print
                        </button>
                        <button @click="showSuccessModal = false"
                                class="flex-1 py-3 text-white font-black text-[9px] uppercase tracking-widest rounded-xl transition-all duration-200 flex items-center justify-center gap-2 active:scale-[0.98]"
                                style="background: linear-gradient(135deg, #808000 0%, #a4a400 100%); box-shadow: 0 4px 16px rgba(128,128,0,0.35);">
                            New Order
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Notifications -->
        <x-notifications />

        <!-- ═══════════════════ STYLES ═══════════════════ -->
        <style>
            [x-cloak] { display: none !important; }

            /* ─── Layout ─── */
            .pos-bg {
                background: #f8fafc;
            }

            /* ─── Header ─── */
            .pos-header {
                background: #ffffff;
                border-bottom: 1px solid #e2e8f0;
                box-shadow: 0 1px 0 rgba(0,0,0,0.04);
            }

            .pos-search-input {
                width: 100%;
                background: #f8fafc;
                border: 1.5px solid #e2e8f0;
                border-radius: 0.75rem;
                font-size: 11px;
                font-weight: 600;
                color: #334155;
                padding: 0.5rem 1rem 0.5rem 2.75rem;
                transition: all 0.2s;
            }
            .pos-search-input:focus {
                border-color: #808000;
                background: #ffffff;
                box-shadow: 0 0 0 3px rgba(128,128,0,0.12);
            }
            .pos-search-input::placeholder { color: #94a3b8; }

            .pos-stat-badge {
                background: #f1f5f9;
                border: 1px solid #e2e8f0;
                color: #64748b;
                border-radius: 0.5rem;
                padding: 0.375rem 0.75rem;
                font-size: 9px;
                font-weight: 700;
            }

            /* ─── Categories ─── */
            .pos-categories {
                background: #ffffff;
                border-bottom: 1px solid #f1f5f9;
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }

            .pos-category-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.375rem;
                padding: 0.375rem 0.875rem;
                border-radius: 100px;
                font-size: 9px;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                background: #f1f5f9;
                color: #64748b;
                border: 1.5px solid transparent;
                transition: all 0.2s ease;
                cursor: pointer;
            }
            .pos-category-pill:hover {
                background: #e2e8f0;
                color: #334155;
            }
            .pos-category-pill.active {
                background: #808000;
                color: #ffffff;
                box-shadow: 0 4px 12px rgba(128,128,0,0.3);
                border-color: transparent;
            }

            /* ─── Menu Cards ─── */
            .pos-menu-card {
                background: #ffffff;
                border-radius: 1rem;
                padding: 0.55rem;
                border: 1.5px solid #f1f5f9;
                box-shadow: 0 1px 4px rgba(0,0,0,0.04);
                transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            }
            .pos-menu-card:hover {
                box-shadow: 0 8px 28px rgba(0,0,0,0.1);
                border-color: rgba(128,128,0,0.25);
                transform: translateY(-3px);
            }
            .pos-menu-card:active {
                transform: scale(0.97);
            }

            .pos-price-chip {
                display: inline-block;
                background: rgba(255,255,255,0.9);
                backdrop-filter: blur(8px);
                border-radius: 8px;
                padding: 0.2rem 0.5rem;
                font-size: 10px;
                font-weight: 900;
                color: #808000;
                border: 1px solid rgba(128,128,0,0.2);
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            }

            .pos-low-stock-badge {
                display: inline-block;
                background: #ef4444;
                color: white;
                font-size: 7px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                padding: 0.2rem 0.5rem;
                border-radius: 100px;
            }

            .pos-empty-icon-bg {
                background: #f8fafc;
                border: 2px dashed #e2e8f0;
            }

            /* ─── Sidebar ─── */
            .pos-sidebar {
                background: #ffffff;
                border-left: 1px solid #e8ecf0;
                box-shadow: -4px 0 24px rgba(0,0,0,0.06);
            }

            .pos-sidebar-header {
                border-bottom: 1px solid #f1f5f9;
                background: #fafbfc;
            }

            /* ─── Cart Items ─── */
            .pos-cart-item {
                background: #f8fafc;
                border: 1.5px solid #f1f5f9;
                border-radius: 0.75rem;
                padding: 0.625rem 0.75rem;
                transition: all 0.2s ease;
            }
            .pos-cart-item:hover {
                border-color: rgba(128,128,0,0.2);
                background: #f5f7f0;
            }

            .pos-empty-cart-icon {
                background: #f8fafc;
                border: 2px dashed #e2e8f0;
                border-radius: 1rem;
            }

            /* ─── Checkout Panel ─── */
            .pos-checkout-panel {
                border-top: 1px solid #f1f5f9;
                background: #fafbfc;
            }

            /* ─── Form Fields ─── */
            .pos-field-label {
                display: block;
                font-size: 7px;
                font-weight: 900;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 0.15em;
                padding-left: 2px;
            }

            .pos-field-input {
                width: 100%;
                background: #ffffff;
                border: 1.5px solid #e2e8f0;
                border-radius: 0.6rem;
                padding: 0.5rem 0.75rem;
                font-size: 10px;
                font-weight: 700;
                color: #334155;
                transition: all 0.2s;
            }
            .pos-field-input:focus {
                border-color: #808000;
                box-shadow: 0 0 0 3px rgba(128,128,0,0.1);
                background: #ffffff;
            }
            .pos-field-input::placeholder { color: #cbd5e1; font-weight: 600; }

            .pos-field-select {
                width: 100%;
                background: #ffffff;
                border: 1.5px solid #e2e8f0;
                border-radius: 0.6rem;
                padding: 0.5rem 0.75rem;
                font-size: 10px;
                font-weight: 700;
                color: #334155;
                transition: all 0.2s;
                appearance: none;
                cursor: pointer;
            }
            .pos-field-select:focus {
                border-color: #808000;
                box-shadow: 0 0 0 3px rgba(128,128,0,0.1);
            }

            /* ─── Discount Row ─── */
            .pos-discount-row {
                background: #f8fafc;
                border: 1.5px solid #e8ecf0;
            }

            /* ─── Totals Card ─── */
            .pos-totals-card {
                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f1 100%);
                border: 1.5px solid rgba(128,128,0,0.12);
            }

            /* ─── Table Selection ─── */
            .pos-table-tile {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                background: #ffffff;
                border: 1.5px solid #e2e8f0;
                border-radius: 0.75rem;
                padding: 0.5rem 0.625rem;
                transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
                cursor: pointer;
            }
            .pos-table-tile:hover:not(:disabled) {
                border-color: rgba(128,128,0,0.3);
                background: #f5f7f0;
                transform: translateY(-1px);
            }
            .pos-table-tile.selected {
                background: linear-gradient(135deg, #808000 0%, #a4a400 100%);
                border-color: transparent;
                box-shadow: 0 4px 12px rgba(128,128,0,0.3);
                transform: scale(1.02);
            }
            .pos-table-tile.occupied {
                background: #fffbeb;
                border-color: #fde68a;
                cursor: not-allowed;
                opacity: 0.8;
            }

            /* ─── Modal Header ─── */
            .pos-modal-header {
                background: linear-gradient(135deg, #808000 0%, #a4a400 100%);
            }

            /* ─── Dark Mode (Scoped) ─── */
            .pos-bg.pos-dark {
                background: #0b1220;
            }

            .pos-dark .pos-header {
                background: #111827;
                border-bottom-color: #1f2937;
                box-shadow: 0 1px 0 rgba(255,255,255,0.03);
            }

            .pos-dark .pos-search-input {
                background: #0f172a;
                border-color: #334155;
                color: #e2e8f0;
            }
            .pos-dark .pos-search-input::placeholder {
                color: #64748b;
            }
            .pos-dark .pos-search-input:focus {
                background: #0b1220;
                border-color: #a4a400;
                box-shadow: 0 0 0 3px rgba(164,128,0,0.18);
            }

            .pos-dark .pos-stat-badge {
                background: #1e293b;
                border-color: #334155;
                color: #cbd5e1;
            }

            .pos-dark .pos-categories {
                background: #111827;
                border-bottom-color: #1f2937;
            }

            .pos-dark .pos-category-pill {
                background: #1e293b;
                color: #cbd5e1;
            }
            .pos-dark .pos-category-pill:hover {
                background: #334155;
                color: #f1f5f9;
            }

            .pos-dark .pos-menu-card {
                background: #111827;
                border-color: #1f2937;
                box-shadow: 0 1px 6px rgba(0,0,0,0.35);
            }
            .pos-dark .pos-menu-card:hover {
                border-color: rgba(164,164,0,0.35);
                box-shadow: 0 10px 28px rgba(0,0,0,0.45);
            }

            .pos-dark .pos-price-chip {
                background: rgba(15,23,42,0.9);
                color: #d9f99d;
                border-color: rgba(164,164,0,0.35);
            }

            .pos-dark .pos-empty-icon-bg,
            .pos-dark .pos-empty-cart-icon {
                background: #0f172a;
                border-color: #334155;
            }

            .pos-dark .pos-sidebar {
                background: #0f172a;
                border-left-color: #1f2937;
                box-shadow: -4px 0 24px rgba(0,0,0,0.45);
            }

            .pos-dark .pos-sidebar-header {
                border-bottom-color: #1f2937;
                background: #111827;
            }

            .pos-dark .pos-cart-item {
                background: #111827;
                border-color: #1f2937;
            }
            .pos-dark .pos-cart-item:hover {
                border-color: rgba(164,164,0,0.35);
                background: #172033;
            }

            .pos-dark .pos-checkout-panel {
                border-top-color: #1f2937;
                background: #0b1220;
            }

            .pos-dark .pos-field-label {
                color: #94a3b8;
            }

            .pos-dark .pos-field-input,
            .pos-dark .pos-field-select {
                background: #111827;
                border-color: #334155;
                color: #e2e8f0;
            }
            .pos-dark .pos-field-input::placeholder {
                color: #64748b;
            }
            .pos-dark .pos-field-input:focus,
            .pos-dark .pos-field-select:focus {
                border-color: #a4a400;
                box-shadow: 0 0 0 3px rgba(164,164,0,0.16);
                background: #0f172a;
            }

            .pos-dark .pos-discount-row {
                background: #111827;
                border-color: #334155;
            }

            .pos-dark .pos-totals-card {
                background: linear-gradient(135deg, #111827 0%, #182235 100%);
                border-color: rgba(164,164,0,0.28);
            }

            .pos-dark .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(148,163,184,0.25);
            }
            .pos-dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(148,163,184,0.42);
            }

            .pos-dark .bg-white,
            .pos-dark .bg-white\/95,
            .pos-dark .bg-white\/90,
            .pos-dark .bg-slate-50,
            .pos-dark .from-white,
            .pos-dark .to-white {
                background-color: #111827 !important;
            }

            .pos-dark .border-slate-100,
            .pos-dark .border-slate-200,
            .pos-dark .border-slate-200\/90,
            .pos-dark .border-slate-300,
            .pos-dark .border-slate-300\/80,
            .pos-dark .border-t,
            .pos-dark .border-l {
                border-color: #334155 !important;
            }

            .pos-dark .text-slate-800,
            .pos-dark .text-slate-700,
            .pos-dark .text-slate-600,
            .pos-dark .text-slate-500 {
                color: #e2e8f0 !important;
            }

            .pos-dark .text-slate-400,
            .pos-dark .text-slate-300 {
                color: #94a3b8 !important;
            }

            .pos-dark option {
                background-color: #111827;
                color: #e2e8f0;
            }

            /* ─── Scrollbar ─── */
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.07); border-radius: 10px; }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.13); }

            .scrollbar-hide::-webkit-scrollbar { display: none; }
            .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

            /* ─── Animations ─── */
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(8px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            .animate-fadeIn {
                animation: fadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
            }

            @keyframes bounceIn {
                0%   { transform: scale(0.85); opacity: 0; }
                60%  { transform: scale(1.05); opacity: 1; }
                100% { transform: scale(1); }
            }
        </style>

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
            
            // Listen for kot-sent event to print KOT
            $wire.on('kot-sent', ({ kotId }) => {
                fetch('/print/kot/' + kotId)
                    .then(response => response.text())
                    .then(html => {
                        window.printKot(html);
                    })
                    .catch(err => console.error('Failed to load KOT:', err));
            });
            
            // KOT Print function
            window.printKot = function(kotHtml) {
                const printWindow = window.open('', '_blank', 'width=300,height=600');
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>KOT Print</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 0; padding: 10px; font-size: 12px; }
                            @media print {
                                body { margin: 0; padding: 0; }
                                @page { margin: 0; size: 80mm auto; }
                            }
                        </style>
                    </head>
                    <body>${kotHtml}</body>
                    </html>
                `);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                setTimeout(() => printWindow.close(), 500);
            };
        </script>
        @endscript
    </div>
</div>
