<x-layout>
    <x-slot:title>মেনু - {{ App\Models\Setting::getValue('site_title', 'Kacchi Bhai') }}</x-slot:title>

    <div class="bg-white min-h-screen">
        <!-- Header -->
        <div class="bg-[#c01c1c] text-white py-12">
            <div class="container-wide">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black mb-2">আমাদের মেনু</h1>
                <p class="text-white/70">সেরা ঐতিহ্যবাহী বাংলা খাবারের সমাহার</p>
            </div>
        </div>

        <!-- Menu Content -->
        <div class="container-wide py-10">
            <livewire:frontend.menu />
        </div>
    </div>
</x-layout>