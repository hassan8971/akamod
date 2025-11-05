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
        Schema::table('orders', function (Blueprint $table) {
            // Foreign key to link to the selected option
            $table->foreignId('packaging_option_id')->nullable()->after('shipping_method')->constrained('packaging_options')->onDelete('set null');
            // Store the actual cost at the time of purchase
            $table->unsignedInteger('packaging_cost')->default(0)->after('shipping_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['packaging_option_id']);
            $table->dropColumn('packaging_option_id');
            $table->dropColumn('packaging_cost');
        });
    }
};
