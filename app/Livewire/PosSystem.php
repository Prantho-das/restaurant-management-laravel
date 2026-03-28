<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PosSystem extends Component
{
    public $search = '';

    public $selectedCategoryId = null;

    public $cart = [];

    // New Order Details
    public $orderType = 'dine_in'; // dine_in, takeaway, delivery

    public $paymentMethod = 'cash'; // cash, card, mobile_pay

    public $tableNumber = '';

    public $customerName = '';

    public $customerPhone = '';

    public $guestCount = 1;

    public $notes = '';

    public $referenceNo = '';

    public $showCart = false;

    // Discount Section
    public $discountValue = 0;

    public $discountType = 'fixed'; // fixed, percentage

    public function with(): array
    {
        $categories = Category::orderBy('priority_order')->get();

        $query = MenuItem::query()
            ->with(['category', 'recipes.ingredient'])
            ->where('is_active', true)
            ->when($this->selectedCategoryId, fn ($q) => $q->where('category_id', $this->selectedCategoryId))
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"));

        $items = $query->get()->map(function ($item) {
            // Calculate stock based on ingredients
            $stock = 999; // Default infinity if no recipes
            if ($item->recipes->isNotEmpty()) {
                foreach ($item->recipes as $recipe) {
                    $ingredientStock = $recipe->ingredient->current_stock;
                    $possibleServings = floor($ingredientStock / max(0.001, $recipe->quantity));
                    $stock = min($stock, $possibleServings);
                }
            }
            $item->available_stock = (int) $stock;

            return $item;
        });

        return [
            'categories' => $categories,
            'items' => $items,
        ];
    }

    public function selectCategory($id)
    {
        $this->selectedCategoryId = $id;
    }

    public function addToCart($itemId)
    {
        $item = MenuItem::with('recipes.ingredient')->find($itemId);

        if (! $item) {
            return;
        }

        // Check stock before adding
        $availableStock = 999;
        if ($item->recipes->isNotEmpty()) {
            foreach ($item->recipes as $recipe) {
                $possibleServings = floor($recipe->ingredient->current_stock / max(0.001, $recipe->quantity));
                $availableStock = min($availableStock, $possibleServings);
            }
        }

        $cartItemKey = array_search($itemId, array_column($this->cart, 'id'));
        $currentQty = ($cartItemKey !== false) ? $this->cart[$cartItemKey]['quantity'] : 0;

        if ($currentQty + 1 > $availableStock) {
            $this->dispatch('notify', type: 'error', message: "Insufficient stock for {$item->name}");

            return;
        }

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

        $this->dispatch('notify', type: 'success', message: "{$item->name} added to order!");
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
    }

    public function updateQuantity($index, $delta)
    {
        $itemId = $this->cart[$index]['id'];
        $item = MenuItem::with('recipes.ingredient')->find($itemId);

        $availableStock = 999;
        if ($item && $item->recipes->isNotEmpty()) {
            foreach ($item->recipes as $recipe) {
                $possibleServings = floor($recipe->ingredient->current_stock / max(0.001, $recipe->quantity));
                $availableStock = min($availableStock, $possibleServings);
            }
        }

        $newQty = $this->cart[$index]['quantity'] + $delta;

        if ($newQty > $availableStock) {
            $this->dispatch('notify', type: 'warning', message: "Stock limit reached for {$this->cart[$index]['name']}");

            return;
        }

        $this->cart[$index]['quantity'] = $newQty;

        if ($this->cart[$index]['quantity'] <= 0) {
            $this->removeFromCart($index);
        }
    }

    #[Computed]
    public function subtotal()
    {
        return collect($this->cart)->sum(fn ($item) => (float) ($item['price'] * $item['quantity']));
    }

    #[Computed]
    public function discountAmount()
    {
        $val = (float) $this->discountValue;
        if ($this->discountType === 'percentage') {
            return ($this->subtotal * $val) / 100;
        }

        return $val;
    }

    #[Computed]
    public function total()
    {
        return max(0, $this->subtotal - $this->discountAmount);
    }

    public function placeOrder()
    {
        if (empty($this->cart)) {
            return;
        }

        $receiptData = null;

        DB::transaction(function () use (&$receiptData) {
            $orderNumber = 'ORD-'.strtoupper(uniqid());

            if ($this->customerPhone) {
                Customer::updateOrCreate(
                    ['phone' => $this->customerPhone],
                    ['name' => $this->customerName ?: 'Walking Customer']
                );
            }

            $order = Order::create([
                'order_number' => $orderNumber,
                'status' => 'pending',
                'subtotal_amount' => $this->subtotal,
                'discount_amount' => $this->discountAmount,
                'discount_type' => $this->discountType,
                'total_amount' => $this->total,
                'order_type' => $this->orderType,
                'payment_method' => $this->paymentMethod,
                'table_number' => $this->orderType === 'dine_in' ? $this->tableNumber : null,
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone,
                'guest_count' => $this->guestCount,
                'notes' => $this->notes,
                'reference_no' => $this->paymentMethod !== 'cash' ? $this->referenceNo : null,
                'user_id' => auth()->id(),
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            $receiptData = $order->load(['items.menuItem', 'user'])->toReceiptArray();
            $receiptData['auto_print'] = (bool) Setting::getValue('pos_auto_print_receipt', false);
        });

        $this->cart = [];
        $this->tableNumber = '';
        $this->customerName = '';
        $this->customerPhone = '';
        $this->notes = '';
        $this->referenceNo = '';
        $this->discountValue = 0;

        // Reset computed property caches
        unset($this->subtotal);
        unset($this->discountAmount);
        unset($this->total);

        $this->dispatch('order-placed');
        $this->dispatch('notify', type: 'success', message: 'Order processed successfully!');

        if ($receiptData) {
            $this->dispatch('print-receipt', receipt: $receiptData);
        }
    }

    public function clearCart()
    {
        $this->cart = [];
    }

    public function processOfflineOrder($cart, $total, $details = [])
    {
        DB::transaction(function () use ($cart, $total, $details) {
            $order = Order::create([
                'order_number' => $details['order_number'] ?? ('ORD-OFF-'.strtoupper(uniqid())),
                'status' => 'pending',
                'subtotal_amount' => $total,
                'total_amount' => $total,
                'order_type' => $details['order_type'] ?? 'takeaway',
                'payment_method' => $details['payment_method'] ?? 'cash',
                'table_number' => $details['table_number'] ?? null,
                'customer_name' => $details['customer_name'] ?? null,
                'reference_no' => $details['reference_no'] ?? null,
                'notes' => ($details['notes'] ?? '').' (Synced from Offline POS)',
                'user_id' => auth()->id(),
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
        });
    }

    public function render()
    {
        return view('livewire.pos-system', $this->with());
    }
}
