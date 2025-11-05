<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // کد تخفیف، مثلا: BAHAR20
            $table->enum('type', ['percent', 'fixed'])->default('fixed'); // نوع: درصدی یا مبلغ ثابت
            $table->unsignedInteger('value'); // مقدار (مثلا 20 برای درصد یا 50000 برای تومان)
            $table->timestamp('starts_at')->nullable(); // تاریخ شروع
            $table->timestamp('expires_at')->nullable(); // تاریخ انقضا
            $table->unsignedInteger('usage_limit')->nullable(); // سقف استفاده کلی
            $table->unsignedInteger('times_used')->default(0); // تعداد دفعات استفاده شده
            $table->unsignedInteger('min_purchase')->default(0); // حداقل خرید برای اعمال (تومان)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
