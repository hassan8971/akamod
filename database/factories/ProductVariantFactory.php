<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{

    protected $model = \App\Models\ProductVariant::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = $this->faker->numberBetween(100, 900) * 1000; // e.g., 500,000 Toman
        
        // 30% chance of having a sale price
        $salePrice = $this->faker->boolean(30) 
            ? $price - ($this->faker->numberBetween(10, 30) * 1000) 
            : null;

        return [
            // product_id will be set by the ProductSeeder
            'size' => $this->faker->randomElement(['Small', 'Medium', 'Large', '40', '41', '42']),
            'color' => $this->faker->colorName,
            'price' => $price,
            'discount_price' => $salePrice,
            'buy_price' => $this->faker->numberBetween(100000, 1000000),
            'stock' => $this->faker->numberBetween(0, 100),
            'buy_source' => 'someone'
        ];
    }
}
