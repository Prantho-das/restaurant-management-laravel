<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Outlet extends Model
{
    /** @use HasFactory<\Database\Factories\OutletFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'timezone',
        'currency',
        'is_active',
    ];

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }
}
