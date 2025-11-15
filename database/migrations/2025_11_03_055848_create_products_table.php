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

            $table->foreignId('admin_id')
                  ->nullable()
                  ->constrained('admins') // Link to 'id' on 'admins' table
                  ->onDelete('set null'); // If admin is deleted, set this to NULL
            
            // Link to the categories table
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            
            // Shared product info
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('care_and_maintenance')->nullable();
            $table->string('product_id')->nullable()->unique(); // Your internal SKU
            $table->string('invoice_number')->nullable()->unique();
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_for_men')->default(false);
            $table->boolean('is_for_women')->default(false);

            
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
