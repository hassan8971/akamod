<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all category IDs to assign to products
        $categoryIds = Category::pluck('id');

        if ($categoryIds->isEmpty()) {
            echo "No categories found. Please seed categories first.\n";
            return;
        }

        // Create 20 products
        Product::factory(20)
            // For each product, create 3 variants
            ->has(ProductVariant::factory()->count(3), 'variants')
            // For each product, create 2 images
            ->has(ProductImage::factory()->count(2), 'images')
            // Create the product
            ->create([
                // Assign a random category ID from the ones we found
                'category_id' => $categoryIds->random(),
            ]);
    }
}
