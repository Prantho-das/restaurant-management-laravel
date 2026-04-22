<?php

namespace App\Livewire\Frontend;

use App\Models\MenuItem;
use Livewire\Component;

class FeaturedMenu extends Component
{
    public $cart = [];

    public function mount()
    {
        $this->cart = session('cart', []);
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

    public function render()
    {
        return view('livewire.frontend.featured-menu', [
            'signatureMenuItems' => MenuItem::where('is_active', true)->take(4)->get(),
        ]);
    }
}
