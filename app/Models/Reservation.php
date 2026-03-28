<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'date',
        'guests',
        'arrangement',
        'notes',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
