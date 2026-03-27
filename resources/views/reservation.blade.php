<x-layout>
    <x-slot:title>Signature Reservation - Royal Dine</x-slot:title>

    <div class="section-padding bg-parchment relative overflow-hidden min-h-screen flex items-center">
        <!-- Background Elements -->
        <div class="absolute inset-0 bg-subtle-pattern opacity-10"></div>
        <div class="absolute -top-[20%] -right-[10%] w-[50%] h-[50%] bg-brand-gold/5 blur-[120px] rounded-full"></div>
        
        <div class="container-wide relative z-10 w-full">
            <div class="grid lg:grid-cols-12 gap-24 items-center">
                <!-- Info -->
                <div class="lg:col-span-5 animate-fade-in-up">
                    <h2 class="text-[11px] font-black tracking-[0.5em] uppercase text-brand-gold mb-10">Exclusive Experience</h2>
                    <h1 class="text-6xl md:text-8xl text-brand-emerald mb-10 leading-none">Book Your <span class="italic text-brand-gold">Presence.</span></h1>
                    <p class="text-lg text-brand-emerald/70 mb-16 leading-relaxed max-w-md">Join us for an evening of quiet luxury and culinary excellence. We provide personalized service for every guest.</p>
                    
                    <div class="flex flex-col gap-10">
                        <div class="flex items-center gap-6">
                            <div class="w-12 h-12 bg-brand-emerald text-parchment flex items-center justify-center rounded-full text-lg">📞</div>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-widest text-brand-emerald mb-1">VIP Concierge</h4>
                                <p class="text-sm text-brand-emerald/50">+880 1234 567890</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-6">
                            <div class="w-12 h-12 bg-brand-gold text-brand-emerald flex items-center justify-center rounded-full text-lg">💎</div>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-widest text-brand-emerald mb-1">Private Events</h4>
                                <p class="text-sm text-brand-emerald/50">events@royaldine.com</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="lg:col-span-7 animate-fade-in-up delay-200">
                    <div class="bg-white p-12 lg:p-20 rounded-2xl shadow-premium relative border border-brand-gold/10 overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-brand-gold/5 blur-3xl rounded-full"></div>
                        
                        <form class="grid grid-cols-2 gap-10 relative z-10">
                            <div class="col-span-2 md:col-span-1 flex flex-col gap-4">
                                <label class="text-[10px] uppercase font-bold tracking-[0.3em] text-brand-gold">Your Name</label>
                                <input type="text" placeholder="SHAHID KHAN" class="border-b-2 border-brand-gold/20 py-4 text-sm font-bold placeholder:text-brand-emerald/20 focus:outline-none focus:border-brand-emerald transition-colors text-brand-emerald">
                            </div>
                            <div class="col-span-2 md:col-span-1 flex flex-col gap-4">
                                <label class="text-[10px] uppercase font-bold tracking-[0.3em] text-brand-gold">Contact Number</label>
                                <input type="tel" placeholder="+880 17... " class="border-b-2 border-brand-gold/20 py-4 text-sm font-bold placeholder:text-brand-emerald/20 focus:outline-none focus:border-brand-emerald transition-colors text-brand-emerald">
                            </div>

                            <div class="col-span-2 md:col-span-1 flex flex-col gap-4">
                                <label class="text-[10px] uppercase font-bold tracking-[0.3em] text-brand-gold">Preferred Date</label>
                                <input type="date" class="border-b-2 border-brand-gold/20 py-4 text-sm font-bold placeholder:text-brand-emerald/20 focus:outline-none focus:border-brand-emerald transition-colors text-brand-emerald">
                            </div>
                            <div class="col-span-2 md:col-span-1 flex flex-col gap-4">
                                <label class="text-[10px] uppercase font-bold tracking-[0.3em] text-brand-gold">Guest List Count</label>
                                <select class="border-b-2 border-brand-gold/20 py-4 text-sm font-bold bg-transparent focus:outline-none focus:border-brand-emerald transition-colors text-brand-emerald">
                                    <option>02 PERS</option>
                                    <option>04 PERS</option>
                                    <option>06 PERS</option>
                                    <option>ROYAL TABLE (10+)</option>
                                </select>
                            </div>

                            <div class="col-span-2 flex flex-col gap-4">
                                <label class="text-[10px] uppercase font-bold tracking-[0.3em] text-brand-gold">Special Arrangement Notes</label>
                                <textarea placeholder="ANY ALLERGIES OR CELEBRATION DETAILS..." rows="2" class="border-b-2 border-brand-gold/20 py-4 text-sm font-bold placeholder:text-brand-emerald/20 focus:outline-none focus:border-brand-emerald transition-colors text-brand-emerald bg-transparent"></textarea>
                            </div>

                            <div class="col-span-2 pt-10">
                                <button type="button" class="btn-royal w-full py-6 text-xs tracking-[0.5em] uppercase shadow-xl hover:shadow-brand-emerald/20">Secure the Table</button>
                                <p class="text-center text-[9px] uppercase font-bold tracking-[0.2em] text-brand-emerald/30 mt-8">Subject to availability. Our concierge will confirm via call.</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
