<x-filament-panels::page>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/dexie/dist/dexie.js"></script>
    <div class="-mx-6 -mt-6 -mb-6 h-[calc(100vh-4rem)]">
        @livewire(\App\Livewire\PosSystem::class)
    </div>
</x-filament-panels::page>
