<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class LandingPageSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Mission & Vision
            'lp_mission_title' => 'Our Mission',
            'lp_mission_subtitle' => 'The Heart of Hospitality',
            'lp_mission_description' => 'To preserve and promote the authentic heritage of Bangladeshi cuisine while providing a royal dining experience that treats every guest like family.',
            'lp_vision_title' => 'Our Vision',
            'lp_vision_subtitle' => 'The Future of Tradition',
            'lp_vision_description' => 'To become the global ambassador of Bangladeshi culinary arts, setting the gold standard for heritage dining and sustainable hospitality.',

            // Re-seeding basic landing page content if missing
            'lp_hero_title' => 'Serving <span class="italic text-brand-gold">Royalty</span> Since Generations.',
            'lp_hero_subtitle' => 'Authentic Traditions',
            'lp_hero_description' => 'Experience the timeless flavors of Bangladesh, where every dish tells a story of heritage and passion.',

            'lp_heritage_title' => 'Traditional Soul, Modern <span class="text-brand-gold italic">Craft.</span>',
            'lp_heritage_subtitle' => 'Our Secret',
            'lp_heritage_description' => 'Our recipes are secrets passed down through generations, refined with modern culinary techniques to bring you the best of both worlds.',

            // Reservation Section
            'lp_reservation_subtitle' => 'টেবিল বুকিং',
            'lp_reservation_title' => 'আপনার জন্য <span class="text-[#c01c1c]">টেবিল</span> রিজার্ভ',
            'lp_reservation_description' => 'প্রাইভেট ইভেন্ট ও কর্পোরেট গাদারিং এর জন্য আমরা সর্বদা প্রস্তুত।',
            'lp_reservation_contact_label' => 'হেল্পলাইন',

            // Reviews Section
            'lp_reviews_subtitle' => 'আমাদের গ্রাহকদের কথা',
            'lp_reviews_title' => 'স্বাদের <span class="text-brand-primary">স্মৃতি</span>',
            'lp_reviews_description' => 'আমাদের খাবারের স্বাদ নিয়ে যারা মুগ্ধ, তাদের কিছু কথা।',

            // Advanced Settings (Multiple Socials & Opening Times)
            'footer_social_links' => json_encode([
                ['platform' => 'facebook', 'url' => '#'],
                ['platform' => 'instagram', 'url' => '#'],
                ['platform' => 'whatsapp', 'url' => '#'],
            ]),
            'footer_opening_hours' => json_encode([
                ['days' => 'Mon - Thu', 'hours' => '12pm - 11pm'],
                ['days' => 'Fri - Sun', 'hours' => '2pm - 12am'],
            ]),
        ];

        foreach ($settings as $key => $value) {
            $group = str_starts_with($key, 'lp_') ? 'landing_page' : 'general';
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group, 'type' => 'string']
            );
        }
    }
}
