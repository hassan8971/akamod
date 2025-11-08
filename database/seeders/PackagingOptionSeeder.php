<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PackagingOption;

class PackagingOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default packaging options
        PackagingOption::firstOrCreate(
            ['name' => 'استاندارد (بدون هزینه)'],
            ['price' => 0, 'is_active' => true]
        );
        
        PackagingOption::firstOrCreate(
            ['name' => 'بسته‌بندی هدیه'],
            ['price' => 25000, 'is_active' => true]
        );
    }
}
