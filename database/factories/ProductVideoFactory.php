<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVideo>
 */
class ProductVideoFactory extends Factory
{
    protected $model = \App\Models\ProductVideo::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Path to a placeholder video (if you have one)
            'path' => 'placeholders/video-placeholder.mp4',
            'alt_text' => 'Product video',
            'order' => 0,
        ];
    }
}
