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
        Schema::create('packaging_options', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "بسته‌بندی هدیه"
            $table->unsignedInteger('price')->default(0); // Price in Toman
            $table->boolean('is_active')->default(true); // To toggle visibility
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packaging_options');
    }
};
