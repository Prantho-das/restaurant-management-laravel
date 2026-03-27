<x-layout>
    <x-slot:title>Signature Ordering - Royal Dine</x-slot:title>

    <div class="section-padding bg-parchment min-h-screen">
        <!-- Background Pattern -->
        <div class="fixed inset-0 bg-subtle-pattern pointer-events-none opacity-5"></div>
        
        <div class="container-wide relative z-10">
            <div class="grid lg:grid-cols-12 gap-16">
                <!-- Selection -->
                <div class="lg:col-span-8 animate-fade-in-up">
                    <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-8">
                        <div>
                            <span class="text-[10px] font-black tracking-[0.4em] uppercase text-brand-gold mb-4 block">Boutique Selection</span>
                            <h1 class="text-5xl md:text-6xl text-brand-emerald leading-tight italic font-serif">Curate Your <span class="text-brand-gold font-normal not-italic">Order</span></h1>
                        </div>
                        <div class="flex gap-4">
                            <button class="bg-brand-emerald text-parchment px-6 py-2 rounded-full text-[10px] font-bold tracking-widest uppercase">Mains</button>
                            <button class="bg-white text-brand-emerald border border-brand-emerald/20 px-6 py-2 rounded-full text-[10px] font-bold tracking-widest uppercase hover:bg-brand-gold/10">Appetizers</button>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-10">
                        <!-- Item 1 -->
                        <div class="bg-white p-8 rounded-2xl shadow-premium border border-brand-gold/20 group">
                            <div class="aspect-video rounded-xl overflow-hidden mb-8 shadow-sm">
                                <img src="/images/placeholders/kacchi_biryani_1774629083139.png" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            </div>
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-2xl text-brand-emerald font-bold">Shahi Mutton Kacchi</h3>
                                <span class="text-sm font-extrabold text-brand-gold">৳ ৯৫০</span>
                            </div>
                            <p class="text-xs text-brand-emerald mb-8 leading-relaxed font-medium opacity-80">The pinnacle of our heritage. Slow-cooked aromatic rice with hand-picked mutton pieces.</p>
                            <button class="w-full py-4 border-2 border-brand-emerald text-brand-emerald text-[11px] uppercase font-black tracking-widest rounded-full hover:bg-brand-emerald hover:text-parchment transition-all shadow-sm">Add to Selection</button>
                        </div>

                        <!-- Item 2 -->
                        <div class="bg-white p-8 rounded-2xl shadow-premium border border-brand-gold/20 group">
                            <div class="aspect-video rounded-xl overflow-hidden mb-8 shadow-sm">
                                <img src="/images/placeholders/bhuna_khichuri_beef_1774629196663.png" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            </div>
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-2xl text-brand-emerald font-bold">Heritage Beef Bhuna</h3>
                                <span class="text-sm font-extrabold text-brand-gold">৳ ৭২০</span>
                            </div>
                            <p class="text-xs text-brand-emerald mb-8 leading-relaxed font-medium opacity-80">A hearty classic served with spicy beef bhuna and fried eggplant. Perfect for corporate lunches.</p>
                            <button class="w-full py-4 border-2 border-brand-emerald text-brand-emerald text-[11px] uppercase font-black tracking-widest rounded-full hover:bg-brand-emerald hover:text-parchment transition-all shadow-sm">Add to Selection</button>
                        </div>
                    </div>
                </div>

                <!-- Bag -->
                <div class="lg:col-span-4 animate-fade-in-up delay-200">
                    <div class="sticky top-32 bg-brand-emerald text-parchment p-10 rounded-3xl shadow-2xl relative overflow-hidden border border-white/10">
                        <div class="absolute inset-0 bg-subtle-pattern opacity-5 pointer-events-none"></div>
                        
                        <div class="relative z-10">
                            <h2 class="text-xs font-black tracking-[0.4em] uppercase text-brand-gold mb-12">Your Selection</h2>
                            
                            <div class="flex flex-col gap-8 mb-12 border-b border-white/20 pb-12">
                                <div class="flex justify-between items-center text-sm">
                                    <div class="flex flex-col">
                                        <span class="font-serif italic text-2xl">Shahi Mutton Kacchi</span>
                                        <span class="text-[9px] uppercase font-bold text-brand-gold tracking-[0.2em] mt-2">Quantity: 01</span>
                                    </div>
                                    <span class="font-bold text-lg">৳ ৯৫০</span>
                                </div>
                            </div>

                            <div class="flex flex-col gap-5 mb-12 text-sm">
                                <div class="flex justify-between text-parchment font-bold uppercase tracking-widest text-[10px]">
                                    <span class="opacity-60">Subtotal</span>
                                    <span>৳ ৯৫০</span>
                                </div>
                                <div class="flex justify-between pt-6 border-t border-white/10 text-brand-gold text-2xl font-serif">
                                    <span class="italic">Total</span>
                                    <span class="font-bold not-italic">৳ ৯৫০</span>
                                </div>
                            </div>

                            <div class="flex flex-col gap-8">
                                <div class="flex flex-col gap-4">
                                    <label class="text-[9px] uppercase font-bold tracking-[0.2em] text-brand-gold opacity-80">Delivery Signature Address</label>
                                    <input type="text" placeholder="SECURE LOCATION..." class="bg-brand-emerald-light/40 border-b-2 border-white/20 py-4 text-xs font-bold placeholder:text-parchment/30 focus:outline-none focus:border-brand-gold transition-colors uppercase tracking-widest px-2">
                                </div>
                                <button class="bg-brand-gold text-brand-emerald py-6 rounded-full text-xs font-black tracking-[0.2em] uppercase hover:bg-parchment transition-all shadow-2xl">Finalize Selection</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
