<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the factory exists before calling it
        if (!Admin::factory()) {
            return;
        }

        // Create one admin user if none exists
        if (Admin::count() == 0) {
            Admin::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'username' => 'admin',
                'mobile' => '09148971850', // شماره موبایل برای ورود ادمین
            ]);
        }
    }
}
