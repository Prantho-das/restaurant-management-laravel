@php
    $cartCount = collect(session('cart', []))->sum('quantity');
@endphp
<div x-data="{ cartCount: {{ $cartCount }} }" 
     @cart-updated.window="cartCount = $event.detail.count"
     class="md:hidden fixed bottom-0 left-0 right-0 z-30 px-3 pb-3">
    <div class="bg-white rounded-full shadow-lg flex items-center justify-around py-2 px-2 border">
        <a href="/" wire:navigate class="flex flex-col items-center p-2 {{ request()->is('/') ? 'text-[#c01c1c]' : 'text-gray-500' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
            <span class="text-[8px] font-bold">হোম</span>
        </a>

        <a href="/menu" wire:navigate class="flex flex-col items-center p-2 {{ request()->is('menu') ? 'text-[#c01c1c]' : 'text-gray-500' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            <span class="text-[8px] font-bold">মেনু</span>
        </a>

        <a href="/order" wire:navigate class="bg-[#c01c1c] text-white p-3 rounded-full -mt-6 shadow-lg">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
        </a>

        <a href="#reservation" class="flex flex-col items-center p-2 text-gray-500">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            <span class="text-[8px] font-bold">বুকিং</span>
        </a>

        <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="flex flex-col items-center p-2 text-gray-500">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
            <span class="text-[8px] font-bold">টপ</span>
        </button>
    </div>
</div>