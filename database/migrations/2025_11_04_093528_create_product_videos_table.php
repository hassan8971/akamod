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
        // Creates the new 'product_videos' table
        Schema::create('product_videos', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to link this to the 'products' table
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            // Path to the stored video file (e.g., 'products/videos/video.mp4')
            $table->string('path'); 
            
            $table->string('alt_text')->nullable(); // For accessibility
            $table->unsignedSmallInteger('order')->default(0); // For sorting
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_videos');
    }
};