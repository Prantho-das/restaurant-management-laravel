<?php

use Livewire\Component;
use App\Models\Category;
use App\Models\MenuItem;

new class extends Component
{
    public $selectedCategoryId = null;

    public function selectCategory($id = null)
    {
        $this->selectedCategoryId = $id;
    }

    public function with(): array
    {
        return [
            'categories' => Category::where('is_active', true)->orderBy('priority_order')->get(),
            'menuItems' => MenuItem::where('is_active', true)
                ->when($this->selectedCategoryId, fn($q) => $q->where('category_id', $this->selectedCategoryId))
                ->get(),
        ];
    }
};
?>

<div>
    <!-- Categories -->
    <div class="flex flex-wrap justify-center gap-10 mb-32 animate-fade-in-up delay-100">
        <button
            wire:click="selectCategory(null)"
            class="text-[11px] font-black tracking-[0.3em] uppercase pb-2 border-b-2 {{ is_null($selectedCategoryId) ? 'border-brand-gold text-brand-emerald' : 'border-transparent text-brand-emerald/40 hover:text-brand-emerald' }} transition-colors">
            All Collections
        </button>
        @foreach($categories as $category)
            <button
                wire:click="selectCategory({{ $category->id }})"
                class="text-[11px] font-black tracking-[0.3em] uppercase pb-2 border-b-2 {{ $selectedCategoryId == $category->id ? 'border-brand-gold text-brand-emerald' : 'border-transparent text-brand-emerald/40 hover:text-brand-emerald' }} transition-colors">
                {{ $category->name }}
            </button>
        @endforeach
    </div>

    <!-- Menu Grid -->
    <div class="grid lg:grid-cols-2 gap-x-24 gap-y-32">
        @foreach($menuItems as $item)
            <div class="flex flex-col md:flex-row gap-12 animate-fade-in-up" wire:key="item-{{ $item->id }}">
                <div class="md:w-1/2 aspect-[4/5] rounded-2xl overflow-hidden shadow-premium">
                    <img src="/images/placeholders/{{ $item->image ?? 'kacchi_biryani_1774629083139.png' }}" class="w-full h-full object-cover">
                </div>
                <div class="md:w-1/2 flex flex-col justify-center">
                    <div class="flex justify-between items-baseline border-b border-brand-gold/20 pb-4 mb-6">
                        <h3 class="text-3xl text-brand-emerald">{{ $item->name }}</h3>
                        <span class="text-sm font-bold text-brand-gold">৳ {{ number_format($item->base_price, 0) }}</span>
                    </div>
                    <p class="text-brand-emerald text-sm leading-loose mb-8 opacity-80">{{ $item->description }}</p>
                    <a href="/order" class="text-[10px] font-black tracking-[0.2em] uppercase text-brand-emerald hover:text-brand-gold transition-all underline decoration-brand-gold/40 underline-offset-8 decoration-2">Reserve for Delivery</a>
                </div>
            </div>
        @endforeach
    </div>
</div>