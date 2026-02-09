<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Pages (Tab-ha)
        Schema::create('app_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g., "Home", "Games"
            $table->string('slug')->unique(); // e.g., "home", "vitrin"
            $table->string('platform')->default('web'); // 'web', 'android', 'ios'
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Sections (Block-ha)
        Schema::create('app_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_page_id')->constrained()->cascadeOnDelete();
            
            // Types: 'slider_main', 'list_horizontal', 'banner_single', 'grid_categories'
            $table->string('type'); 
            
            $table->string('title')->nullable(); // Onvane Bakhsh
            $table->string('sub_title')->nullable();
            
            // Logic: 'auto' (query based) OR 'manual' (hand-picked ids)
            $table->string('source_type')->default('auto'); 
            
            // Configuration JSON (Flexible storage for links, filters, counts, or manual IDs)
            // Example: { "limit": 10, "sort": "newest", "category_id": 5, "manual_ids": [1,2,3] }
            $table->json('config')->nullable();
            
            $table->integer('sort_order')->default(0); // Baraye Drag & Drop
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('app_sections');
        Schema::dropIfExists('app_pages');
    }
};