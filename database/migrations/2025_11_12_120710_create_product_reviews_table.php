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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            // Link to the user who wrote the review
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Link to the product being reviewed
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // Add parent_id for nested replies
            $table->foreignId('parent_id')
                  ->nullable() // Top-level comments have NULL
                  ->constrained('product_reviews') // References the 'id' on the SAME table
                  ->onDelete('cascade'); // If parent comment is deleted, delete all replies
            
            $table->unsignedTinyInteger('rating')->nullable(); // 1, 2, 3, 4, or 5
            $table->text('comment');
            
            // Admin approval (set to true for now to auto-approve)
            $table->boolean('is_approved')->default(true); 
            
            $table->timestamps();
            

            // Prevent a user from reviewing the same product multiple times
            $table->unique(['user_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
