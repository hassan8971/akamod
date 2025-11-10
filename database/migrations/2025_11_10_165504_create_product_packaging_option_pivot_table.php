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
        Schema::create('product_packaging_option_pivot', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('packaging_option_id')->constrained('packaging_options')->onDelete('cascade');
            // کلید اصلی ترکیبی برای جلوگیری از ثبت رکورد تکراری
            $table->primary(['product_id', 'packaging_option_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_packaging_option_pivot');
    }
};
