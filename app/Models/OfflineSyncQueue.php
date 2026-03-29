<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfflineSyncQueue extends Model
{
    protected $table = 'offline_sync_queue';

    protected $fillable = [
        'order_number',
        'status',
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
        'items',
        'sync_token',
        'synced_at',
        'sync_error',
        'sync_attempts',
    ];

    protected $casts = [
        'subtotal_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'guest_count' => 'integer',
        'items' => 'array',
        'synced_at' => 'datetime',
        'sync_attempts' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
