<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserPermissionSeeder::class,
            CategorySeeder::class,
            CustomerSeeder::class,
            ExpenseSeeder::class,
            IngredientSeeder::class,
            MenuItemSeeder::class,
            OutletSeeder::class,
            SettingSeeder::class,
            SupplierSeeder::class,
            WastageSeeder::class,
            RestaurantSeeder::class,
        ]);
    }
}
