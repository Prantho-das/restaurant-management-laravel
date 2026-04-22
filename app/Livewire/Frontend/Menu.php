<?php

namespace App\Livewire\Frontend;

use App\Models\Category;
use App\Models\MenuItem;
use Livewire\Attributes\Url;
use Livewire\Component;

class Menu extends Component
{
    #[Url]
    public ?int $selectedCategoryId = null;

    #[Url]
    public string $search = '';

    public int $perPage = 12;

    public array $cart = [];

    public function mount(): void
    {
        $this->cart = session('cart', []);

        // Trigger conversion event
        $this->dispatch('conversion-event', name: 'ViewContent', data: [
            'content_type' => 'product',
        ]);
    }

    public function selectCategory(?int $id = null): void
    {
        $this->selectedCategoryId = $id;
        $this->perPage = 12;
    }

    public function updatedSearch(): void
    {
        $this->perPage = 12;
    }

    public function loadMore(): void
    {
        $this->perPage += 12;
    }

    public function addToCart(int $itemId): void
    {
        $item = MenuItem::find($itemId);
        if (! $item) {
            return;
        }

        $cartItemKey = array_search($itemId, array_column($this->cart, 'id'));

        if ($cartItemKey !== false) {
            $this->cart[$cartItemKey]['quantity']++;
        } else {
            $this->cart[] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => (float) $item->final_price,
                'quantity' => 1,
            ];
        }

        session()->put('cart', $this->cart);
        $count = collect($this->cart)->sum('quantity');
        $this->js("window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: {$count} } }))");

        // Trigger conversion event
        $this->dispatch('conversion-event', name: 'AddToCart', data: [
            'content_name' => $item->name,
            'content_ids' => [$item->id],
            'content_type' => 'product',
            'value' => (float) $item->final_price,
            'currency' => 'BDT',
        ]);

        $this->dispatch('notify', ['type' => 'success', 'message' => "{$item->name} added to cart!"]);
    }

    public function getCartCountProperty(): int
    {
        return collect($this->cart)->sum('quantity');
    }

    public function getSubtotalProperty(): float
    {
        return collect($this->cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
    }

    public function with(): array
    {
        $query = MenuItem::where('is_active', true)
            ->when($this->selectedCategoryId, fn ($q) => $q->where('category_id', $this->selectedCategoryId))
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'));

        $total = $query->count();
        $menuItems = $query->take($this->perPage)->get();

        return [
            'categories' => Category::where('is_active', true)->orderBy('priority_order')->get(),
            'menuItems' => $menuItems,
            'totalCount' => $total,
            'hasMoreItems' => $total > $this->perPage,
        ];
    }

    public function render()
    {
        return view('livewire.frontend.menu', $this->with());
    }
}
