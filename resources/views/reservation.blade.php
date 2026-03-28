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
                        
                        <livewire:frontend.reservation-form />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
