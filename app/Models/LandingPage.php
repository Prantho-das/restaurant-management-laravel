<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    /** @use HasFactory<\Database\Factories\LandingPageFactory> */
    use HasFactory;

    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_description',
        'hero_image',
        'heritage_title',
        'heritage_subtitle',
        'heritage_description',
        'heritage_image_1',
        'heritage_image_2',
        'heritage_image_3',
        'secret_title',
        'secret_subtitle',
        'secret_description',
        'visual_story_title',
        'visual_story_subtitle',
        'is_active',
    ];
}
