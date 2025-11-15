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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "خانه"
            $table->string('link_url'); // e.g., "/" or "/categories/clothing"
            
            // For nested/dropdown menus
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('menu_items') // Self-referencing
                  ->onDelete('cascade'); // If parent is deleted, delete children
            
            // To distinguish between 'header' and 'footer' menus
            $table->string('menu_group')->default('main_header'); 
            
            // For sorting (e.g., Home=1, Products=2)
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
