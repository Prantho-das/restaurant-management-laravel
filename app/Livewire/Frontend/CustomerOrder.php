<?php

namespace App\Livewire\Frontend;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\BkashService;
use App\Services\MetaService;
use App\Services\SslcommerzeService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CustomerOrder extends Component
{
    public $search = '';

    public $selectedCategoryId = null;

    public $cart = [];

    // Customer details
    public $customerName = '';

    public $customerPhone = '';

    public $deliveryAddress = '';

    public $paymentMethod = 'cash';

    public $referenceNo = '';

    public $notes = '';

    // Order placed state
    public $orderPlaced = false;

    public $confirmedOrderNumber = '';

    public $confirmedTotal = 0;

    public $confirmedOrderItems = [];

    public function mount()
    {
        $this->cart = session('cart', []);

        $paymentStatus = request()->query('payment');
        if ($paymentStatus === 'success') {
            $orderId = session('confirmed_order_id');
            if ($orderId) {
                $order = Order::with('items.menuItem')->find($orderId);
                if ($order && $order->status === 'completed') {
                    $this->confirmedOrderNumber = $order->order_number;
                    $this->confirmedTotal = $order->total_amount;
                    $this->confirmedOrderItems = $order->items->map(function ($item) {
                        return [
                            'id' => $item->menu_item_id,
                            'name' => $item->menuItem->name ?? 'Item',
                            'price' => $item->price,
                            'quantity' => $item->quantity,
                        ];
                    })->toArray();
                    $this->orderPlaced = true;

                    // Trigger conversion events for payment success
                    $this->dispatch('conversion-event', name: 'Purchase', data: [
                        'value' => (float) $this->confirmedTotal,
                        'currency' => 'BDT',
                        'content_type' => 'product',
                        'content_ids' => collect($this->confirmedOrderItems)->pluck('id')->toArray(),
                    ]);

                    // Server-side (CAPI)
                    app(MetaService::class)->sendEvent('Purchase', [
                        'value' => (float) $this->confirmedTotal,
                        'currency' => 'BDT',
                    ], [
                        'fn' => hash('sha256', strtolower(trim($order->customer_name))),
                    ]);

                    $this->cart = [];
                    $this->syncCartToSession();
                    session()->forget(['confirmed_order_id', 'payment_origin']);

                    return;
                }
            }
        } elseif ($paymentStatus === 'failed') {
            session()->forget('payment_origin');
        }

        $addItemId = request()->query('add');
        if ($addItemId) {
            $this->addToCart((int) $addItemId);

            return $this->redirect('/order', navigate: false);
        }
    }

    protected function syncCartToSession()
    {
        session()->put('cart', $this->cart);
        $this->dispatchCartUpdate();
    }

    protected function dispatchCartUpdate()
    {
        $count = collect($this->cart)->sum('quantity');
        $this->js("window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: {$count} } }))");
    }

    public function with(): array
    {
        $categories = Category::where('is_active', true)->orderBy('priority_order')->get();

        $items = MenuItem::query()
            ->with('category')
            ->where('is_active', true)
            ->when($this->selectedCategoryId, fn ($q) => $q->where('category_id', $this->selectedCategoryId))
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->get();

        return [
            'categories' => $categories,
            'items' => $items,
        ];
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

        $this->syncCartToSession();

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

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->syncCartToSession();
    }

    public function updateQuantity($index, $delta)
    {
        $this->cart[$index]['quantity'] += $delta;

        if ($this->cart[$index]['quantity'] <= 0) {
            $this->removeFromCart($index);

            return;
        }

        $this->syncCartToSession();
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
    }

    public function getCartCountProperty()
    {
        return collect($this->cart)->sum('quantity');
    }

    public function placeOrder()
    {
        $this->validate([
            'customerName' => 'required|min:2',
            'customerPhone' => 'required|min:6',
            'deliveryAddress' => 'required|min:5',
        ], [
            'customerName.required' => 'Please enter your name.',
            'customerPhone.required' => 'Please enter your phone number.',
            'deliveryAddress.required' => 'Delivery address is required.',
        ]);

        if (empty($this->cart)) {
            return;
        }

        $order = null;

        DB::transaction(function () use (&$order) {
            $order = Order::create([
                'order_number' => 'ORD-'.strtoupper(uniqid()),
                'status' => 'pending',
                'subtotal_amount' => $this->subtotal,
                'discount_amount' => 0,
                'discount_type' => 'fixed',
                'total_amount' => $this->subtotal,
                'order_type' => 'delivery',
                'payment_method' => $this->paymentMethod,
                'customer_name' => $this->customerName,
                'notes' => collect([
                    "Phone: {$this->customerPhone}",
                    "Delivery Address: {$this->deliveryAddress}",
                    $this->notes,
                ])->filter()->implode("\n"),
                'reference_no' => null,
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            $this->confirmedOrderNumber = $order->order_number;
            $this->confirmedTotal = $order->total_amount;
            $this->confirmedOrderItems = $this->cart;
        });

        if (! $order) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Failed to create order. Please try again.']);

            return;
        }

        // Redirect to gateway if not cash
        if ($this->paymentMethod === 'mobile_pay') {
            session(['payment_origin' => 'frontend', 'confirmed_order_id' => $order->id]);
            $bkashService = app(BkashService::class);
            $result = $bkashService->createPayment($order);

            if ($result && isset($result['checkout_url'])) {
                $order->update(['reference_no' => $result['payment_id']]);
                session(['bkash_payment_id' => $result['payment_id']]);

                return redirect()->to($result['checkout_url']);
            }

            $this->dispatch('notify', ['type' => 'error', 'message' => 'Failed to connect to bKash.']);

            return;
        } elseif ($this->paymentMethod === 'card') {
            session(['payment_origin' => 'frontend', 'confirmed_order_id' => $order->id]);
            $sslcommerzeService = app(SslcommerzeService::class);
            $checkoutUrl = $sslcommerzeService->createCheckoutUrl($order);

            if ($checkoutUrl) {
                return redirect()->to($checkoutUrl);
            }

            $this->dispatch('notify', ['type' => 'error', 'message' => 'Failed to connect to SSLCommerz.']);

            return;
        }

        // Cash flow logic
        $this->cart = [];
        $this->syncCartToSession();
        $this->orderPlaced = true;

        // Trigger conversion events for cash success
        $this->dispatch('conversion-event', name: 'Purchase', data: [
            'value' => (float) $this->confirmedTotal,
            'currency' => 'BDT',
            'content_type' => 'product',
            'content_ids' => collect($this->confirmedOrderItems)->pluck('id')->toArray(),
        ]);

        // Server-side (CAPI)
        app(MetaService::class)->sendEvent('Purchase', [
            'value' => (float) $this->confirmedTotal,
            'currency' => 'BDT',
        ], [
            'fn' => hash('sha256', strtolower(trim($this->customerName))),
            'ph' => hash('sha256', preg_replace('/[^0-9]/', '', $this->customerPhone)),
        ]);
    }

    public function startNewOrder()
    {
        $this->reset(['customerName', 'customerPhone', 'deliveryAddress', 'paymentMethod', 'referenceNo', 'notes', 'confirmedOrderNumber', 'confirmedTotal', 'confirmedOrderItems', 'orderPlaced', 'search', 'selectedCategoryId']);
        $this->cart = [];
        $this->syncCartToSession();
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->syncCartToSession();
    }

    public function render()
    {
        return view('livewire.frontend.customer-order', $this->with());
    }
}
