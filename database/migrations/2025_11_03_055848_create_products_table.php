<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            // Link to the categories table
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            
            // Shared product info
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('product_id')->nullable()->unique(); // Your internal SKU
            $table->string('boxing_type')->nullable(); // e.g., "Box of 6", "Single Item"
            $table->boolean('is_visible')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
