<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{

    protected $model = \App\Models\ProductImage::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Use placeholder images
            'path' => 'placeholders/image-' . $this->faker->numberBetween(1, 5) . '.jpg',
            'alt_text' => $this->faker->sentence,
            'order' => 0,
        ];
    }
}
