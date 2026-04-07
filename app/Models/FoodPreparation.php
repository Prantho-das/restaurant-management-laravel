<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FoodPreparation extends Model
{
    use LogsActivity;

    protected $fillable = [
        'outlet_id',
        'user_id',
        'menu_item_id',
        'quantity',
        'prepared_at',
        'notes',
    ];

    protected $casts = [
        'prepared_at' => 'datetime',
        'quantity' => 'decimal:4',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(FoodPreparationItem::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    protected static function booted(): void
    {
        static::creating(function (FoodPreparation $foodPreparation) {
            if (Auth::check()) {
                $foodPreparation->user_id = Auth::id();
                // If menu item has an outlet, use it
                if ($foodPreparation->menuItem && $foodPreparation->menuItem->outlet_id) {
                    $foodPreparation->outlet_id = $foodPreparation->menuItem->outlet_id;
                }
            }
        });
    }
}
