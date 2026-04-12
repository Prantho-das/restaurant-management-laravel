<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KotOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'kot_number',
        'status',
        'sent_at',
        'preparing_at',
        'ready_at',
        'served_at',
        'cancelled_at',
        'cancellation_reason',
        'sent_by',
        'prepared_by',
        'ready_by',
        'served_by',
        'cancelled_by',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'preparing_at' => 'datetime',
            'ready_at' => 'datetime',
            'served_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(KotOrderItem::class);
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function readyBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ready_by');
    }

    public function servedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    /**
     * Generate a unique KOT number
     */
    public static function generateKotNumber(): string
    {
        $prefix = 'KOT';
        $date = now()->format('Ymd');
        $random = strtoupper(uniqid());

        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Send KOT to kitchen
     */
    public function sendToKitchen(int $userId): bool
    {
        return $this->update([
            'status' => 'pending',
            'sent_at' => now(),
            'sent_by' => $userId,
        ]);
    }

    /**
     * Mark KOT as preparing
     */
    public function startPreparing(int $userId): bool
    {
        return $this->update([
            'status' => 'preparing',
            'preparing_at' => now(),
            'prepared_by' => $userId,
        ]);
    }

    /**
     * Mark KOT as ready
     */
    public function markAsReady(int $userId): bool
    {
        return $this->update([
            'status' => 'ready',
            'ready_at' => now(),
            'ready_by' => $userId,
        ]);
    }

    /**
     * Mark KOT as served
     */
    public function markAsServed(int $userId): bool
    {
        return $this->update([
            'status' => 'served',
            'served_at' => now(),
            'served_by' => $userId,
        ]);
    }

    /**
     * Cancel KOT
     */
    public function cancel(int $userId, ?string $reason = null): bool
    {
        return $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => $userId,
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Get elapsed time since sent
     */
    public function getElapsedTime(): string
    {
        if (!$this->sent_at) {
            return '-';
        }

        $diff = now()->diff($this->sent_at);

        if ($diff->h > 0) {
            return "{$diff->h}h {$diff->i}m";
        }

        return "{$diff->i}m";
    }
}
