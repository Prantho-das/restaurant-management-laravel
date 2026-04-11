<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Run this seeder to populate the database with test data.
     */
    public function run(): void
    {
        $this->call([
            TestSeeder::class,
        ]);
    }
}
