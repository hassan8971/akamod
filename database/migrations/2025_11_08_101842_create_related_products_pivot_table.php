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
        Schema::create('related_products_pivot', function (Blueprint $table) {
            // The main product
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');
            
            // The product it is related to
            $table->foreignId('related_product_id')
                  ->constrained('products')
                  ->onDelete('cascade');

            // Set primary key to prevent duplicates
            $table->primary(['product_id', 'related_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('related_products_pivot');
    }
};