<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PremadeStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_id',
        'available_quantity',
    ];

    protected function casts(): array
    {
        return [
            'available_quantity' => 'decimal:4',
        ];
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}

