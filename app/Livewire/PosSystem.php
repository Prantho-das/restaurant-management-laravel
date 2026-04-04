<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Setting;
use App\Models\Table;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PosSystem extends Component
{
    public string $search = '';

    public $selectedCategoryId = null;

    public int $perPage = 16;

    public array $cart = [];

    // New Order Details
    public $orderType = 'dine_in'; // dine_in, takeaway, delivery

    public $paymentMethod = 'cash'; // cash, card, bkash, sslcommerze

    public $tableNumber = '';

    public $customerName = '';

    public $customerPhone = '';

    public $guestCount = 1;

    public $notes = '';

    public $referenceNo = '';

    public $showCart = false;

    // Split Payment Section
    public bool $isSplitPayment = false;

    public array $paymentSplits = [];

    // Discount Section
    public $discountValue = 0;

    public $discountType = 'fixed'; // fixed, percentage

    // Payment method configurations
    public array $paymentMethodConfigs = [
        'cash' => [
            'icon' => '<svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>',
            'color' => 'emerald',
            'label' => 'Cash',
        ],
        'card' => [
            'icon' => '<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>',
            'color' => 'rose',
            'label' => 'Card',
        ],
        'bkash' => [
            'icon' => '<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>',
            'color' => 'sky',
            'label' => 'bKash',
        ],
        'pubali' => [
            'icon' => '<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>',
            'color' => 'blue',
            'label' => 'Pubali Bank',
        ],
        'city_bank' => [
            'icon' => '<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>',
            'color' => 'indigo',
            'label' => 'City Bank',
        ],
        'sslcommerze' => [
            'icon' => '<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>',
            'color' => 'cyan',
            'label' => 'SSLCommerze',
        ],
    ];

    // Online payment gateways that require internet
    public array $onlinePaymentMethods = ['bkash', 'sslcommerze'];

    protected function getCategories()
    {
        return Category::orderBy('priority_order')->get();
    }

    protected function getTables()
    {
        return Table::orderBy('name')->get();
    }

    protected function getItems(): array
    {
        $query = MenuItem::query()
            ->with(['category', 'recipes.ingredient'])
            ->where('is_active', true)
            ->when($this->selectedCategoryId, fn ($q) => $q->where('category_id', $this->selectedCategoryId))
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"));

        $total = $query->count();

        $items = $query->take($this->perPage)->get()->map(function ($item) {
            // Calculate stock based on ingredients
            $stock = 999; // Default infinity if no recipes
            if ($item->recipes->isNotEmpty()) {
                foreach ($item->recipes as $recipe) {
                    if ($recipe->ingredient) {
                        $ingredientStock = $recipe->ingredient->current_stock;
                        $possibleServings = floor($ingredientStock / max(0.001, $recipe->quantity));
                        $stock = min($stock, $possibleServings);
                    }
                }
            }
            $item->available_stock = (int) $stock;

            return $item;
        });

        return [
            'items' => $items,
            'totalItems' => $total,
            'hasMoreItems' => $total > $this->perPage,
        ];
    }

    public function selectCategory($id): void
    {
        $this->selectedCategoryId = $id;
        $this->perPage = 16;
    }

    public function updatedSearch(): void
    {
        $this->perPage = 16;
    }

    public function loadMore(): void
    {
        $this->perPage += 16;
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

    public function updatedIsSplitPayment($value)
    {
        if ($value && empty($this->paymentSplits)) {
            $this->addPaymentSplit();
            $this->addPaymentSplit();
        }
    }

    public function addPaymentSplit()
    {
        $this->paymentSplits[] = [
            'method' => 'cash',
            'amount' => 0,
            'reference_no' => '',
        ];
    }

    public function removePaymentSplit($index)
    {
        unset($this->paymentSplits[$index]);
        $this->paymentSplits = array_values($this->paymentSplits);
    }

    #[Computed]
    public function enabledPaymentMethods(): array
    {
        $methods = [
            'cash' => 'Cash',
            'card' => 'Card',
            'bkash' => 'bKash',
            'pubali' => 'Pubali Bank',
            'city_bank' => 'City Bank',
        ];

        return $methods;
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

    /**
     * Create order as completed (cash/card payments).
     */
    protected function createAndCompleteOrder(): array
    {
        if (empty($this->cart)) {
            return ['success' => false, 'message' => 'Cart is empty'];
        }

        if ($this->isSplitPayment) {
            $splitTotal = collect($this->paymentSplits)->sum('amount');
            if (abs($splitTotal - $this->total) > 0.01) {
                return ['success' => false, 'message' => "Split total ({$splitTotal}) does not match order total ({$this->total})"];
            }
        }

        $orderNumber = 'ORD-'.strtoupper(uniqid());

        $orderData = [
            'order_number' => $orderNumber,
            'status' => 'completed',
            'payment_status' => 'paid',
            'subtotal_amount' => $this->subtotal,
            'discount_amount' => $this->discountAmount,
            'discount_type' => $this->discountType,
            'total_amount' => $this->total,
            'order_type' => $this->orderType,
            'payment_method' => $this->isSplitPayment ? 'split' : $this->paymentMethod,
            'table_number' => $this->tableNumber ?: null,
            'customer_name' => $this->customerName,
            'customer_phone' => $this->customerPhone,
            'guest_count' => $this->guestCount,
            'notes' => $this->notes,
            'reference_no' => (! $this->isSplitPayment && $this->paymentMethod !== 'cash') ? $this->referenceNo : null,
            'user_id' => auth()->id(),
        ];

        $items = $this->cart;
        $receiptData = null;
        $orderId = null;

        DB::transaction(function () use (&$receiptData, $orderData, $items, &$orderId) {
            if ($this->customerPhone) {
                Customer::updateOrCreate(
                    ['phone' => $this->customerPhone],
                    ['name' => $this->customerName ?: 'Walking Customer']
                );
            }

            $order = Order::create($orderData);
            $orderId = $order->id;

            // Save payments
            if ($this->isSplitPayment) {
                foreach ($this->paymentSplits as $split) {
                    OrderPayment::create([
                        'order_id' => $order->id,
                        'payment_method' => $split['method'],
                        'amount' => $split['amount'],
                        'reference_no' => $split['reference_no'],
                    ]);
                }
            } else {
                OrderPayment::create([
                    'order_id' => $order->id,
                    'payment_method' => $this->paymentMethod,
                    'amount' => $this->total,
                    'reference_no' => $this->referenceNo,
                ]);
            }

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // Deduct stock explicitly since OrderObserver won't catch status transition for directly completed orders
            app(InventoryService::class)->deductStockForOrder($order->load('items.menuItem.recipes.ingredient'));

            $receiptData = $order->load(['items.menuItem', 'user', 'payments'])->toReceiptArray();
            $receiptData['auto_print'] = (bool) Setting::getValue('pos_auto_print_receipt', false);

            // Add split info to receipt if needed
            if ($this->isSplitPayment) {
                $receiptData['payments'] = $order->payments->map(fn ($p) => [
                    'method' => $this->paymentMethodConfigs[$p->payment_method]['label'] ?? $p->payment_method,
                    'amount' => $p->amount,
                ])->toArray();
            }
        });

        return [
            'success' => true,
            'order' => Order::find($orderId),
            'receipt' => $receiptData,
        ];
    }

    /**
     * Create pending order for online payment (bKash, SSLCommerze).
     */
    protected function createPendingOrder(): ?Order
    {
        if (empty($this->cart)) {
            $this->dispatch('notify', type: 'error', message: 'Cart is empty');

            return null;
        }

        $orderNumber = 'ORD-'.strtoupper(uniqid());

        $orderData = [
            'order_number' => $orderNumber,
            'status' => 'pending',
            'payment_status' => 'pending',
            'subtotal_amount' => $this->subtotal,
            'discount_amount' => $this->discountAmount,
            'discount_type' => $this->discountType,
            'total_amount' => $this->total,
            'order_type' => $this->orderType,
            'payment_method' => $this->paymentMethod,
            'table_number' => $this->tableNumber ?: null,
            'customer_name' => $this->customerName,
            'customer_phone' => $this->customerPhone,
            'guest_count' => $this->guestCount,
            'notes' => $this->notes,
            'user_id' => auth()->id(),
        ];

        $items = $this->cart;
        $orderId = null;

        DB::transaction(function () use ($orderData, $items, &$orderId) {
            if ($this->customerPhone) {
                Customer::updateOrCreate(
                    ['phone' => $this->customerPhone],
                    ['name' => $this->customerName ?: 'Walking Customer']
                );
            }

            $order = Order::create($orderData);
            $orderId = $order->id;

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
        });

        return Order::find($orderId);
    }

    /**
     * Place order - handles both direct (cash/card) and manual (bkash/sslcommerze) flows for the POS operator.
     */
    public function placeOrder(): mixed
    {
        $result = $this->createAndCompleteOrder();

        if (! $result['success']) {
            $this->dispatch('notify', type: 'error', message: $result['message']);

            return null;
        }

        $order = $result['order'];
        $receiptData = $result['receipt'];

        $this->clearCart();
        $this->reset(['tableNumber', 'customerName', 'customerPhone', 'notes', 'referenceNo', 'discountValue', 'discountType', 'orderType', 'paymentMethod', 'guestCount', 'isSplitPayment', 'paymentSplits']);

        $this->dispatch('order-placed', orderNumber: $order->order_number);

        if ($receiptData) {
            $this->dispatch('print-receipt', receipt: $receiptData);
        }

        return null;
    }

    public function clearCart()
    {
        $this->cart = [];
    }

    public function render()
    {
        $itemData = $this->getItems();

        return view('livewire.pos-system', [
            'categories' => $this->getCategories(),
            'items' => $itemData['items'],
            'totalItems' => $itemData['totalItems'],
            'hasMoreItems' => $itemData['hasMoreItems'],
            'enabledPaymentMethods' => $this->enabledPaymentMethods,
            'tables' => $this->getTables(),
        ]);
    }
}
