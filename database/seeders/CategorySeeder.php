<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create top-level categories
        $clothing = Category::factory()->create(['name' => 'پوشاک', 'slug' => 'clothing']);
        $shoes = Category::factory()->create(['name' => 'کفش', 'slug' => 'shoes']);
        
        // Create nested categories
        Category::factory()->create([
            'name' => 'تی‌شرت مردانه',
            'slug' => 'mens-tshirt',
            'parent_id' => $clothing->id
        ]);
        
        Category::factory()->create([
            'name' => 'کفش ورزشی',
            'slug' => 'sneakers',
            'parent_id' => $shoes->id
        ]);
    }
}
