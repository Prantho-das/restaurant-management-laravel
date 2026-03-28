<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Table extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'capacity',
        'status',
        'outlet_id',
    ];

    public function getQrCodeUrlAttribute(): string
    {
        return url('/table/'.$this->slug);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }
}
