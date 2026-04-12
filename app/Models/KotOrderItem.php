<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KotOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'kot_order_id',
        'order_item_id',
        'menu_item_id',
        'item_name',
        'quantity',
        'notes',
        'status',
        'preparing_at',
        'ready_at',
        'served_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'preparing_at' => 'datetime',
            'ready_at' => 'datetime',
            'served_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function kotOrder(): BelongsTo
    {
        return $this->belongsTo(KotOrder::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * Start preparing this item
     */
    public function startPreparing(): bool
    {
        return $this->update([
            'status' => 'preparing',
            'preparing_at' => now(),
        ]);
    }

    /**
     * Mark item as ready
     */
    public function markAsReady(): bool
    {
        return $this->update([
            'status' => 'ready',
            'ready_at' => now(),
        ]);
    }

    /**
     * Mark item as served
     */
    public function markAsServed(): bool
    {
        return $this->update([
            'status' => 'served',
            'served_at' => now(),
        ]);
    }

    /**
     * Cancel this item
     */
    public function cancel(?string $reason = null): bool
    {
        return $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }
}
