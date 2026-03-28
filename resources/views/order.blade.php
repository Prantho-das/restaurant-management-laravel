<x-layout>
    <x-slot:title>Order Online - {{ App\Models\Setting::getValue('site_title', 'Royal Dine') }}</x-slot:title>

    <livewire:frontend.customer-order />
</x-layout>
