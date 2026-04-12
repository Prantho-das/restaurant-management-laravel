<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'order_number',
        'status',
        'is_stock_deducted',
        'subtotal_amount',
        'discount_amount',
        'discount_type',
        'total_amount',
        'order_type',
        'payment_method',
        'table_number',
        'customer_name',
        'customer_phone',
        'guest_count',
        'notes',
        'reference_no',
        'user_id',
        'payment_status',
        'transaction_id',
        'gateway_response',
        'paid_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_stock_deducted' => 'boolean',
            'subtotal_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'guest_count' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Check if order is paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid' || $this->status === 'completed';
    }

    /**
     * Mark order as paid.
     */
    public function markAsPaid(string $transactionId, ?string $gatewayResponse = null): bool
    {
        return $this->update([
            'status' => 'completed',
            'payment_status' => 'paid',
            'transaction_id' => $transactionId,
            'gateway_response' => $gatewayResponse,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark order as payment failed.
     */
    public function markAsFailed(?string $gatewayResponse = null): bool
    {
        return $this->update([
            'status' => 'failed',
            'payment_status' => 'failed',
            'gateway_response' => $gatewayResponse,
        ]);
    }

    /**
     * Check if payment was made via online gateway.
     */
    public function isOnlinePayment(): bool
    {
        return in_array($this->payment_method, ['bkash', 'sslcommerze']);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get KOT orders for this order.
     */
    public function kotOrders(): HasMany
    {
        return $this->hasMany(KotOrder::class);
    }

    /**
     * Check if order has been sent to kitchen.
     */
    public function hasKot(): bool
    {
        return $this->kotOrders()->exists();
    }

    /**
     * Get the latest KOT for this order.
     */
    public function latestKot(): ?KotOrder
    {
        return $this->kotOrders()->latest()->first();
    }

    /**
     * @return array<string, mixed>
     */
    public function toReceiptArray(): array
    {
        return [
            'order_number' => $this->order_number,
            'datetime' => $this->created_at->format('d M Y, h:i A'),
            'customer_name' => $this->customer_name ?: 'Walking Customer',
            'order_type' => $this->order_type,
            'payment_method' => $this->payment_method,
            'table_number' => $this->table_number,
            'reference_no' => $this->reference_no,
            'cashier' => $this->user?->name ?? 'Staff',
            'items' => $this->items->map(fn ($item) => [
                'name' => $item->menuItem?->name ?? 'Unknown Item',
                'qty' => $item->quantity,
                'price' => $item->price,
                'subtotal' => (float) $item->price * $item->quantity,
            ])->toArray(),
            'subtotal' => (float) $this->subtotal_amount,
            'discount' => (float) $this->discount_amount,
            'discount_type' => $this->discount_type,
            'total' => (float) $this->total_amount,
            'restaurant_name' => Setting::getValue('site_name', Setting::getValue('site_title', config('app.name'))),
            'restaurant_address' => Setting::getValue('footer_address', ''),
            'restaurant_phone' => Setting::getValue('footer_phone', ''),
        ];
    }
}
