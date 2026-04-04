<?php

namespace App\Livewire\Frontend;

use App\Models\Category;
use App\Models\MenuItem;
use Livewire\Attributes\Url;
use Livewire\Component;

class Menu extends Component
{
    #[Url]
    public $selectedCategoryId = null;

    public $cart = [];

    public function mount()
    {
        $this->cart = session('cart', []);
    }

    public function selectCategory($id = null)
    {
        $this->selectedCategoryId = $id;
    }

    public function addToCart($itemId)
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
        $this->dispatch('notify', ['type' => 'success', 'message' => "{$item->name} added to cart!"]);
    }

    public function getCartCountProperty()
    {
        return collect($this->cart)->sum('quantity');
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
    }

    public function with(): array
    {
        return [
            'categories' => Category::where('is_active', true)->orderBy('priority_order')->get(),
            'menuItems' => MenuItem::where('is_active', true)
                ->when($this->selectedCategoryId, fn ($q) => $q->where('category_id', $this->selectedCategoryId))
                ->get(),
        ];
    }

    public function render()
    {
        return view('livewire.frontend.menu', $this->with());
    }
}
