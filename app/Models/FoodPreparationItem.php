<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodPreparationItem extends Model
{
    protected $fillable = [
        'food_preparation_id',
        'ingredient_id',
        'quantity',
    ];

    public function foodPreparation(): BelongsTo
    {
        return $this->belongsTo(FoodPreparation::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    protected static function booted(): void
    {
        static::created(function (FoodPreparationItem $item) {
            $ingredient = $item->ingredient;
            if ($ingredient) {
                // Deduct from ingredient stock
                $ingredient->decrement('current_stock', $item->quantity);

                // Create inventory log
                InventoryLog::create([
                    'ingredient_id' => $ingredient->id,
                    'type' => 'Deduction',
                    'quantity' => $item->quantity,
                    'note' => "Food Preparation: Used in {$item->foodPreparation->menuItem->name}",
                    'user_id' => $item->foodPreparation->user_id,
                ]);
            }
        });
    }
}
