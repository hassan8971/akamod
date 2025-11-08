<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    protected $model = \App\Models\Admin::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Admin User',
            'email' => $this->faker->unique()->safeEmail(),
            'mobile' => $this->faker->unique()->e164PhoneNumber(),
            'password' => Hash::make('password'), // Default password is 'password'
        ];
    }
}
