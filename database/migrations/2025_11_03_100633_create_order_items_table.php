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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            
            // Link to the variant, but allow it to be deleted (set null)
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            
            // Denormalize data for long-term order history
            // This is crucial. If the product name/price changes later, the order history remains correct.
            $table->string('product_name');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('price'); // Price per item, in cents
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
