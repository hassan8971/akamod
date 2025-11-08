<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the factory exists before calling it
        if (!User::factory()) {
            return;
        }
        
        // Create one test user if none exists
        if (User::count() == 0) {
            User::factory()->create([
                'name' => 'Zac',
                'mobile' => '09911465506', // شماره موبایل برای ورود کاربر
                'email' => 'user@example.com',
            ]);
        }
    }
}
