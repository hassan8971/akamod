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
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('method_key')->unique(); // e.g., 'pishtaz'
            $table->string('title'); // e.g., 'پست پیشتاز'
            $table->integer('cost')->default(0); // e.g., 35000
            $table->string('description')->nullable(); // e.g., 'ارسال سریع به سراسر کشور'
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
