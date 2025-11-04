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
        Schema::table('categories', function (Blueprint $table) {
            // Add parent_id for nested categories
            $table->foreignId('parent_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('categories') // Self-referencing foreign key
                  ->onDelete('set null'); // If a parent is deleted, its children become top-level

            // Add image path
            $table->string('image_path')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Drop in reverse order
            $table->dropColumn('image_path');
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
