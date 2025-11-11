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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Allow guest orders
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // We'll store addresses in the 'addresses' table
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->onDelete('set null');
            $table->foreignId('billing_address_id')->nullable()->constrained('addresses')->onDelete('set null');

            $table->string('status')->default('pending'); // pending, processing, shipped, cancelled

            $table->string('discount_code')->nullable();
            $table->unsignedInteger('discount_amount')->default(0);
            
            $table->unsignedInteger('subtotal'); // Store in cents
            $table->unsignedInteger('shipping_cost')->default(0);
            $table->unsignedInteger('total'); // subtotal + shipping + taxes
            
            $table->string('payment_method')->nullable();
            $table->string('transaction_code')->nullable();
            $table->string('payment_status')->default('pending'); // pending, paid, failed


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
