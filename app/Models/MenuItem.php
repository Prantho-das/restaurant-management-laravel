<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    /** @use HasFactory<\Database\Factories\MenuItemFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'outlet_id',
        'name',
        'slug',
        'description',
        'base_price',
        'discount_price',
        'tax_rate',
        'image',
        'is_active',
        'sku',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->discount_price ?? $this->base_price);
    }
}
