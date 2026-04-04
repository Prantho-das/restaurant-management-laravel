<x-layout>
    <x-slot:title>Order Online - {{ App\Models\Setting::getValue('site_title', 'Royal Dine') }}</x-slot:title>

    <div class="bg-parchment min-h-screen">
        <livewire:frontend.customer-order />
    </div>
</x-layout>