@php
    $siteTitle = App\Models\Setting::getValue('site_title', 'Royal Dine');
    $aboutText = App\Models\Setting::getValue('footer_about_text', 'Curating the finest heritage recipes of Bengal with a commitment to culinary excellence and royal hospitality.');
    $address = App\Models\Setting::getValue('footer_address', 'Banani Rd 11, Block H, Dhaka');
    $phone = App\Models\Setting::getValue('footer_phone', '+880 1234 567890');
    
    $socialLinks = json_decode(App\Models\Setting::getValue('footer_social_links', '[]'), true) ?: [];
    $openingHours = json_decode(App\Models\Setting::getValue('footer_opening_hours', '[]'), true) ?: [];
    
    $customPages = \App\Models\Page::where('is_active', true)->where('show_in_footer', true)->get();

    $socialIcons = [
        'facebook' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        'instagram' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259 0 3.259 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
        'whatsapp' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.412-.003 6.557-5.338 11.892-11.893 11.892-1.912 0-3.79-.459-5.474-1.332l-6.523 1.54zm5.426-3.705l.344.204c1.288.763 2.593 1.164 3.876 1.165 5.451 0 9.891-4.439 9.893-9.891.001-2.646-1.03-5.132-2.903-7.005-1.871-1.871-4.356-2.901-7.001-2.902-5.451 0-9.891 4.44-9.893 9.892 0 2.025.533 4.004 1.547 5.746l.225.387-1.111 4.056 4.154-.984zm11.393-6.208c-.287-.144-1.705-.842-1.97-.938-.264-.096-.456-.144-.648.144-.191.288-.742.938-.909 1.13-.167.191-.334.215-.621.071-.287-.144-1.21-.446-2.305-1.424-.853-.761-1.428-1.701-1.595-1.989-.168-.287-.018-.443.125-.586.13-.13.287-.336.431-.504.144-.168.192-.288.287-.48.096-.192.048-.36-.024-.504-.071-.144-.648-1.56-.887-2.136-.232-.558-.469-.482-.648-.491-.167-.008-.36-.01-.552-.01-.192 0-.503.072-.767.36-.264.288-1.007.984-1.007 2.399 0 1.416 1.031 2.784 1.175 2.976.145.192 2.028 3.097 4.912 4.342.686.296 1.222.473 1.638.605.69.219 1.318.188 1.815.115.553-.081 1.705-.696 1.946-1.368.24-.672.24-1.248.167-1.368-.072-.12-.264-.192-.551-.336z"/></svg>',
        'twitter' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>',
        'youtube' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
    ];
@endphp

<footer class="bg-brand-emerald text-parchment mt-40 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-radial from-brand-emerald-light/20 to-transparent"></div>
    <div class="absolute inset-0 bg-subtle-pattern opacity-5 pointer-events-none"></div>
    
    <div class="relative py-24 lg:py-32">
        <div class="container-wide grid md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-16 relative z-10">
            <!-- Brand -->
            <div class="lg:col-span-1">
                <h3 class="text-3xl font-serif italic mb-8">{{ $siteTitle }}</h3>
                <p class="text-parchment/70 text-sm leading-relaxed mb-10 max-w-xs">
                    {{ $aboutText }}
                </p>
                @if(!empty($socialLinks))
                <div class="flex gap-4">
                    @foreach($socialLinks as $link)
                        <a href="{{ $link['url'] }}" target="_blank" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-brand-gold hover:bg-brand-gold hover:text-brand-emerald transition-all duration-300">
                            {!! $socialIcons[$link['platform']] ?? '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>' !!}
                        </a>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Links -->
            <div>
                <h4 class="text-brand-gold uppercase text-[11px] font-black tracking-[0.3em] mb-10">Experience</h4>
                <ul class="flex flex-col gap-5 text-sm font-medium">
                    <li><a href="/" wire:navigate class="hover:text-brand-gold transition-colors duration-300 hover:translate-x-1 inline-flex">Fine Dining</a></li>
                    <li><a href="/menu" wire:navigate class="hover:text-brand-gold transition-colors duration-300 hover:translate-x-1 inline-flex">Signature Menu</a></li>
                    <li><a href="/reservation" wire:navigate class="hover:text-brand-gold transition-colors duration-300 hover:translate-x-1 inline-flex">Private Booking</a></li>
                    <li><a href="/order" wire:navigate class="hover:text-brand-gold transition-colors duration-300 hover:translate-x-1 inline-flex">Order Online</a></li>
                </ul>
            </div>

            <!-- Connection & Dynamic Pages -->
            <div>
                <h4 class="text-brand-gold uppercase text-[11px] font-black tracking-[0.3em] mb-10">Connection</h4>
                <ul class="flex flex-col gap-6 text-sm font-medium text-parchment/80">
                    <li class="flex flex-col gap-1">
                        <span class="text-brand-gold/60 text-[10px] uppercase font-bold tracking-widest">Address</span>
                        <span>{{ $address }}</span>
                    </li>
                    <li class="flex flex-col gap-1">
                        <span class="text-brand-gold/60 text-[10px] uppercase font-bold tracking-widest">Inquiry</span>
                        <span>{{ $phone }}</span>
                    </li>
                    @if($customPages->isNotEmpty())
                        <li class="pt-4 border-t border-white/10 flex flex-col gap-4">
                            @foreach($customPages as $p)
                                <a href="/{{ $p->slug }}" wire:navigate class="text-parchment/60 hover:text-brand-gold transition-colors duration-300 uppercase text-[10px] font-bold tracking-widest">{{ $p->title }}</a>
                            @endforeach
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Hours -->
            <div>
                <h4 class="text-brand-gold uppercase text-[11px] font-black tracking-[0.3em] mb-10">Availability</h4>
                <ul class="flex flex-col gap-4 text-sm font-medium text-parchment/80">
                    @forelse($openingHours as $row)
                        <li class="flex justify-between items-center border-b border-white/10 pb-3">
                            <span>{{ $row['days'] }}</span>
                            <span class="text-brand-gold">{{ $row['hours'] }}</span>
                        </li>
                    @empty
                        <li class="flex justify-between items-center border-b border-white/10 pb-3">
                            <span>Everyday</span>
                            <span class="text-brand-gold">12pm - 11pm</span>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-white/5 py-8">
        <div class="container-wide flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] uppercase font-bold tracking-widest text-parchment/40">
            <p>&copy; {{ date('Y') }} {{ $siteTitle }}. All rights reserved.</p>
            <p>Designed & Developed by <span class="text-brand-gold">SKD</span></p>
        </div>
    </div>
</footer>
