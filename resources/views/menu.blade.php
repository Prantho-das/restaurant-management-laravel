<x-layout>
    <x-slot:title>Our Culinary Menu - Royal Dine</x-slot:title>

    <div class="bg-parchment relative overflow-hidden">
        <!-- Background decoration -->
        <div class="absolute top-0 left-0 w-[40%] h-[30%] bg-brand-emerald/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-[30%] h-[40%] bg-brand-gold/5 rounded-full blur-3xl"></div>
        
        <div class="section-padding pt-28 lg:pt-32 relative z-10">
            <div class="container-wide">
                <!-- Header -->
                <div class="text-center max-w-3xl mx-auto mb-16 lg:mb-24">
                    <span class="inline-block text-brand-gold font-bold tracking-[0.5em] uppercase text-[10px] mb-6 lg:mb-8">Curated Selection</span>
                    <h1 class="text-4xl md:text-6xl lg:text-7xl text-brand-emerald mb-6 lg:mb-8 leading-none">The <span class="italic text-brand-gold">Signature</span> Menu</h1>
                    <p class="text-brand-emerald text-base lg:text-lg leading-relaxed opacity-80 max-w-xl mx-auto">A refined journey through the six seasons of Bengal, highlighting the best of our heritage recipes.</p>
                </div>

                <!-- Livewire Menu Component -->
                <livewire:frontend.menu />
            </div>
        </div>
    </div>
</x-layout>