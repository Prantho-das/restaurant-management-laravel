<footer class="bg-brand-emerald text-parchment mt-40">
    <div class="relative py-24 lg:py-32">
        <div class="absolute inset-0 bg-subtle-pattern opacity-5 pointer-events-none"></div>
        
        <div class="container-wide grid md:grid-cols-4 gap-16 relative z-10">
            <!-- Brand -->
            <div class="md:col-span-1">
                <h3 class="text-3xl font-serif italic mb-8">{{ App\Models\Setting::getValue('site_name', 'Royal Dine') }}</h3>
                <p class="text-parchment/70 text-sm leading-relaxed mb-10 max-w-xs">
                    {{ App\Models\Setting::getValue('footer_about_text', 'Curating the finest heritage recipes of Bengal with a commitment to culinary excellence and royal hospitality.') }}
                </p>
                <div class="flex gap-6">
                    <a href="{{ App\Models\Setting::getValue('social_instagram_url', '#') }}" target="_blank" class="text-brand-gold hover:text-parchment transition-colors uppercase text-[10px] font-black tracking-widest">Instagram</a>
                    <a href="{{ App\Models\Setting::getValue('social_facebook_url', '#') }}" target="_blank" class="text-brand-gold hover:text-parchment transition-colors uppercase text-[10px] font-black tracking-widest">Facebook</a>
                </div>
            </div>

            <!-- Links -->
            <div class="md:col-span-1">
                <h4 class="text-brand-gold uppercase text-[11px] font-black tracking-[0.3em] mb-10">Experience</h4>
                <ul class="flex flex-col gap-5 text-sm font-medium">
                    <li><a href="/" wire:navigate class="hover:text-brand-gold transition-colors">Fine Dining</a></li>
                    <li><a href="/menu" wire:navigate class="hover:text-brand-gold transition-colors">Signature Menu</a></li>
                    <li><a href="/reservation" wire:navigate class="hover:text-brand-gold transition-colors">Private Booking</a></li>
                    <li><a href="/order" wire:navigate class="hover:text-brand-gold transition-colors">Order Online</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="md:col-span-1">
                <h4 class="text-brand-gold uppercase text-[11px] font-black tracking-[0.3em] mb-10">Connection</h4>
                <ul class="flex flex-col gap-6 text-sm font-medium text-parchment/80">
                    <li class="flex flex-col gap-1">
                        <span class="text-brand-gold/60 text-[10px] uppercase font-bold tracking-widest">Address</span>
                        <span>{{ App\Models\Setting::getValue('footer_address', 'Banani Rd 11, Block H, Dhaka') }}</span>
                    </li>
                    <li class="flex flex-col gap-1">
                        <span class="text-brand-gold/60 text-[10px] uppercase font-bold tracking-widest">Inquiry</span>
                        <span>{{ App\Models\Setting::getValue('footer_phone', '+880 1234 567890') }}</span>
                    </li>
                </ul>
            </div>

            <!-- Hours -->
            <div class="md:col-span-1">
                <h4 class="text-brand-gold uppercase text-[11px] font-black tracking-[0.3em] mb-10">Availability</h4>
                <ul class="flex flex-col gap-4 text-sm font-medium text-parchment/80">
                    <li class="flex justify-between border-b border-white/10 pb-2">
                        <span>Mon - Thu</span>
                        <span>{{ App\Models\Setting::getValue('footer_hours_mon_thu', '12pm - 11pm') }}</span>
                    </li>
                    <li class="flex justify-between border-b border-white/10 pb-2">
                        <span>Fri - Sun</span>
                        <span>{{ App\Models\Setting::getValue('footer_hours_fri_sun', '2pm - 12am') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-white/5 py-10">
        <div class="container-wide flex flex-col md:flex-row justify-between items-center gap-6 text-[10px] uppercase font-bold tracking-widest text-parchment/40">
            <p>&copy; {{ date('Y') }} {{ App\Models\Setting::getValue('site_name', 'Royal Dine') }}. All rights reserved. | Designed & Developed by <span class="text-brand-gold">SKD</span> (Phone: 01794188835)</p>
            <div class="flex gap-10">
                <a href="#" class="hover:text-brand-gold">Terms</a>
                <a href="#" class="hover:text-brand-gold">Privacy</a>
            </div>
        </div>
    </div>
</footer>
