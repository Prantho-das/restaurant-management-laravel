<x-layout>
    <x-slot:title>Our Culinary Menu - Royal Dine</x-slot:title>

    <div class="section-padding bg-parchment">
        <div class="container-wide">
            <!-- Header -->
            <div class="text-center max-w-3xl mx-auto mb-32 animate-fade-in-up">
                <span class="inline-block text-brand-gold font-bold tracking-[0.5em] uppercase text-[10px] mb-8">Curated Selection</span>
                <h1 class="text-6xl md:text-8xl text-brand-emerald mb-8 leading-none">The <span class="italic text-brand-gold">Signature</span> Menu</h1>
                <p class="text-brand-emerald text-lg leading-relaxed opacity-80">A refined journey through the six seasons of Bengal, highlighting the best of our heritage recipes.</p>
            </div>

            <!-- Livewire Menu Component -->
            <livewire:frontend.menu />
        </div>
    </div>
</x-layout>
