<x-layout>
    <x-slot:title>{{ App\Models\Setting::getValue('menu_page_title', 'আমাদের মেনু') }} - {{ App\Models\Setting::getValue('site_title', 'Royal Dine') }}</x-slot:title>

    <div class="bg-white min-h-screen">
        <!-- Header -->
        <div class="bg-[#c01c1c] text-white py-12">
            <div class="container-wide">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black mb-2">{{ App\Models\Setting::getValue('menu_page_title', 'আমাদের মেনু') }}</h1>
                <p class="text-white/70">{{ App\Models\Setting::getValue('menu_page_description', 'সেরা ঐতিহ্যবাহী বাংলা খাবারের সমাহার') }}</p>
            </div>
        </div>

        <!-- Menu Content -->
        <div class="container-wide py-10">
            <livewire:frontend.menu />
        </div>
    </div>
</x-layout>