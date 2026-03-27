<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
        ]);

        $outlet = \App\Models\Outlet::create([
            'name' => 'Royal Dine Dhaka',
            'address' => 'Banani, Dhaka',
            'phone' => '01700000000',
        ]);

        $categories = [
            ['name' => 'Royal Biryani', 'slug' => 'royal-biryani'],
            ['name' => 'Heritage Curries', 'slug' => 'heritage-curries'],
            ['name' => 'Artisan Snacks', 'slug' => 'artisan-snacks'],
            ['name' => 'Sweet Traditions', 'slug' => 'sweet-traditions'],
        ];

        foreach ($categories as $catData) {
            $category = \App\Models\Category::create($catData);

            if ($catData['name'] === 'Royal Biryani') {
                \App\Models\MenuItem::create([
                    'category_id' => $category->id,
                    'outlet_id' => $outlet->id,
                    'name' => 'Shahi Mutton Kacchi',
                    'slug' => 'shahi-mutton-kacchi',
                    'description' => 'Old Dhaka style slow-cooked mutton with saffron infused basmati rice.',
                    'base_price' => 950,
                    'image' => 'kacchi_biryani_1774629083139.png',
                ]);
            }

            if ($catData['name'] === 'Heritage Curries') {
                \App\Models\MenuItem::create([
                    'category_id' => $category->id,
                    'outlet_id' => $outlet->id,
                    'name' => 'Heritage Beef Bhuna',
                    'slug' => 'heritage-beef-bhuna',
                    'description' => 'A robust, slow-simmered beef curry with caramelized onions and hand-ground spices.',
                    'base_price' => 720,
                    'image' => 'bhuna_khichuri_beef_1774629196663.png',
                ]);
                \App\Models\MenuItem::create([
                    'category_id' => $category->id,
                    'outlet_id' => $outlet->id,
                    'name' => 'Chittagong Mezban',
                    'slug' => 'chittagong-mezban',
                    'description' => 'Spicy and authentic slow-cooked beef with ground whole spices.',
                    'base_price' => 850,
                    'image' => 'gallery_mutton_curry_1774630607713.png',
                ]);
            }

            if ($catData['name'] === 'Artisan Snacks') {
                \App\Models\MenuItem::create([
                    'category_id' => $category->id,
                    'outlet_id' => $outlet->id,
                    'name' => 'Boutique Fuchka',
                    'slug' => 'boutique-fuchka',
                    'description' => 'Artisan street-snack serve with premium tamarind infusion.',
                    'base_price' => 180,
                    'image' => 'gallery_fuchka_1774630415473.png',
                ]);
            }
        }
    }
}
