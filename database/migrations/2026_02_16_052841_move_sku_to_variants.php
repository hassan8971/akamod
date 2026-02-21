<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // حذف از جدول محصولات
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });

        // اضافه کردن به جدول واریانت‌ها
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('sku')->nullable()->unique()->after('id'); // شناسه منحصر به فرد هر واریانت
            $table->text('qr_code')->nullable()->after('sku'); // متن یا مسیر QR Code
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_id')->nullable();
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['sku', 'qr_code']);
        });
    }
};
