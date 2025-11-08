<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call all the seeders in the correct dependency order
        $this->call([
            AdminSeeder::class,
            UserSeeder::class, // Assuming this seeds your default user
            PackagingOptionSeeder::class,
            DiscountSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class, // This seeder will also handle variants, images, and videos
        ]);
    }
}
