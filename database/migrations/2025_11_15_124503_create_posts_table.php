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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->foreignId('blog_category_id')->nullable()->constrained('blog_categories')->onDelete('set null');
            
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content'); // For Rich Text Editor
            $table->string('featured_image_path')->nullable();
            
            // SEO Fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            // Status & Publish Date
            $table->enum('status', ['published', 'draft'])->default('draft');
            $table->timestamp('published_at')->nullable(); // For scheduled posts
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
