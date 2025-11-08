<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{

    protected $model = \App\Models\Product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'محصول ' . $this->faker->words(3, true);
        return [
            'category_id' => Category::factory(), // Creates a new one or use existing
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->randomNumber(5),
            'product_id' => $this->faker->unique()->ean8(),
            'description' => $this->faker->paragraph(5),
            'is_visible' => true,
        ];
    }
}
