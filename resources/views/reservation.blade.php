<x-layout>
    <x-slot:title>Signature Reservation - {{ App\Models\Setting::getValue('site_title', 'Royal Dine') }}</x-slot:title>

    <div class="bg-brand-bg-white relative overflow-hidden min-h-screen flex items-center">
        <!-- Background Elements -->
        <div class="absolute inset-0 bg-subtle-pattern opacity-10"></div>
        <div class="absolute -top-[20%] -right-[10%] w-[50%] h-[50%] bg-brand-gold/5 blur-[120px] rounded-full"></div>
        <div class="absolute top-[40%] -left-[10%] w-[30%] h-[30%] bg-brand-primary/5 blur-[100px] rounded-full"></div>
        
        <div class="container-wide relative z-10 w-full py-24 lg:py-32">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-20 items-center">
                <!-- Info -->
                <div class="lg:col-span-5">
                    <div class="flex items-center gap-3 mb-6 lg:mb-8">
                        <div class="h-px w-8 bg-brand-primary"></div>
                        <span class="text-[11px] font-black tracking-[0.5em] uppercase text-brand-primary">{{ App\Models\Setting::getValue('reservation_badge', 'Exclusive Experience') }}</span>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl lg:text-6xl xl:text-7xl text-brand-text-dark mb-6 lg:mb-8 leading-none">{!! App\Models\Setting::getValue('reservation_title', 'Book Your <span class="italic text-brand-primary">Presence.</span>') !!}</h1>
                    <p class="text-lg text-brand-text-gray mb-10 lg:mb-14 leading-relaxed max-w-md">{{ App\Models\Setting::getValue('reservation_description', 'Join us for an evening of quiet luxury and culinary excellence. We provide personalized service for every guest.') }}</p>
                    
                    <div class="flex flex-col gap-6 lg:gap-8">
                        <div class="flex items-center gap-4 p-5 bg-white rounded-2xl shadow-card hover:shadow-xl transition-all duration-300 border border-brand-primary/5">
                            <div class="w-12 h-12 bg-brand-primary/10 rounded-full flex items-center justify-center text-brand-primary">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-widest text-brand-primary mb-1">{{ App\Models\Setting::getValue('reservation_contact_1_label', 'VIP Concierge') }}</h4>
                                <p class="text-sm text-brand-text-dark font-medium">{{ App\Models\Setting::getValue('footer_phone', '+880 1234 567890') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-5 bg-white rounded-2xl shadow-card hover:shadow-xl transition-all duration-300 border border-brand-primary/5">
                            <div class="w-12 h-12 bg-brand-gold/10 rounded-full flex items-center justify-center text-brand-gold">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-widest text-brand-text-dark mb-1">{{ App\Models\Setting::getValue('reservation_contact_2_label', 'Private Events') }}</h4>
                                <p class="text-sm text-brand-text-gray">{{ App\Models\Setting::getValue('reservation_email', 'events@royaldine.com') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="lg:col-span-7">
                    <div class="bg-white p-8 lg:p-12 xl:p-14 rounded-3xl shadow-2xl relative overflow-hidden border border-brand-primary/5">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-brand-gold/5 blur-3xl rounded-full"></div>
                        <div class="absolute bottom-0 left-0 w-40 h-40 bg-brand-primary/5 blur-3xl rounded-full"></div>
                        
                        <livewire:frontend.reservation-form />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>