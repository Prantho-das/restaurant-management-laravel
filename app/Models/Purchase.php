<?php

namespace App\Models;

use App\Services\InventoryService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Purchase extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'reference_no',
        'supplier_id',
        'user_id',
        'purchase_date',
        'status',
        'total_amount',
        'discount',
        'notes',
        'is_stock_updated',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::created(function (Purchase $purchase) {
            if ($purchase->is_stock_updated || $purchase->status !== 'received') {
                return;
            }

            self::handleReceivedStatus($purchase);
        });

        static::updated(function (Purchase $purchase) {
            if ($purchase->is_stock_updated || ! $purchase->isDirty('status') || $purchase->status !== 'received' || $purchase->getOriginal('status') === 'received') {
                return;
            }

            self::handleReceivedStatus($purchase);
        });
    }

    protected static function handleReceivedStatus(Purchase $purchase): void
    {
        $purchase->loadMissing(['items.ingredient', 'supplier']);

        app(InventoryService::class)->addStockFromPurchase($purchase);

        \App\Models\Expense::create([
            'category' => 'Purchase',
            'title' => 'Purchase from '.($purchase->supplier?->name ?? 'Supplier'),
            'description' => "Reference: #{$purchase->reference_no}",
            'amount' => $purchase->total_amount,
            'date' => $purchase->purchase_date ?? now(),
            'payment_method' => 'Cash',
            'reference_no' => $purchase->reference_no,
            'user_id' => $purchase->user_id,
        ]);

        DB::table('purchases')->where('id', $purchase->id)->update(['is_stock_updated' => true]);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }
}