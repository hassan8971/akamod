<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Discount;
use Carbon\Carbon;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Discount::firstOrCreate(
            ['code' => 'BAHAR'],
            [
                'type' => 'percent',
                'value' => 10, // 10%
                'min_purchase' => 100000, // حداقل خرید ۱۰۰ هزار تومان
                'expires_at' => Carbon::now()->addMonth(), // تا یک ماه دیگر
                'is_active' => true,
            ]
        );

        Discount::firstOrCreate(
            ['code' => 'HEZAR'],
            [
                'type' => 'fixed',
                'value' => 50000, // ۵۰ هزار تومان ثابت
                'min_purchase' => 500000, // حداقل خرید ۵۰۰ هزار تومان
                'usage_limit' => 100, // فقط ۱۰۰ بار
                'is_active' => true,
            ]
        );
    }
}
