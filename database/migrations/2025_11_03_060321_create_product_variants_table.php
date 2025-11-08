<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // The specific properties
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            
            // Price & Stock belong to the VARIANT, not the product
            $table->unsignedInteger('price'); // Store as cents (e.g., 1000 = $10.00)
            $table->unsignedInteger('discount_price')->nullable();
            $table->unsignedInteger('buy_price')->nullable();
            $table->unsignedInteger('stock')->default(0); // Stock quantity
            $table->string('buy_source')->nullable(); // Stock quantity
            

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
