<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->decimal('overall_rating', 3, 1)->change();
            $table->decimal('vehicle_rating', 3, 1)->change();
            $table->decimal('driver_rating', 3, 1)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedTinyInteger('overall_rating')->change();
            $table->unsignedTinyInteger('vehicle_rating')->change();
            $table->unsignedTinyInteger('driver_rating')->nullable()->change();
        });
    }
};
