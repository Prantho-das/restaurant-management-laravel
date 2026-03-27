<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\LandingPage::create([
            'hero_title' => 'Serving <span class="text-brand-gold italic">Royalty</span> Since Generations.',
            'hero_subtitle' => 'Authentic Traditions',
            'hero_description' => 'Experience the timeless flavors of Bangladesh, meticulously curated in an atmosphere of refined elegance. From the heart of Dhaka to your table.',
            'hero_image' => 'restaurant_interior_1774629009066.png',

            'heritage_title' => 'Traditional Soul, Modern <span class="text-brand-gold">Craft.</span>',
            'heritage_subtitle' => 'Our Secret',
            'heritage_description' => 'Our recipes are secrets passed down through generations. We slow-cook our Biryani in heavy brass pots, ensuring every grain of rice absorbs the essence of the meat and spices.',
            'heritage_image_1' => 'kacchi_biryani_1774629083139.png',
            'heritage_image_2' => 'bhuna_khichuri_beef_1774629196663.png',
            'heritage_image_3' => 'gallery_fuchka_1774630415473.png',

            'secret_title' => 'Heritage Spices',
            'secret_subtitle' => '01.',
            'secret_description' => 'Sourced directly from local farmers in the heart of Sylhet and Chittagong.',

            'visual_story_title' => 'The <span class="italic">Atmosphere</span>',
            'visual_story_subtitle' => 'Visual Story',
            'is_active' => true,
        ]);
    }
}
