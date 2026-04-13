<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_active',
        'show_in_footer',
        'show_in_header',
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean',
        'show_in_footer' => 'boolean',
        'show_in_header' => 'boolean',
    ];
}
