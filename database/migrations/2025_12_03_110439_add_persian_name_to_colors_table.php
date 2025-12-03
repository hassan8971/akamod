<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('colors', function (Blueprint $table) {
            // اضافه کردن ستون نام فارسی بعد از نام انگلیسی
            $table->string('persian_name')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('colors', function (Blueprint $table) {
            $table->dropColumn('persian_name');
        });
    }
};