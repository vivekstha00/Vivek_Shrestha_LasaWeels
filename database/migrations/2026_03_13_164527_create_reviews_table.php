<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedBigInteger('driver_id')->nullable();

            $table->unsignedTinyInteger('overall_rating');
            $table->text('overall_review')->nullable();

            $table->unsignedTinyInteger('vehicle_rating');
            $table->text('vehicle_review')->nullable();

            $table->unsignedTinyInteger('driver_rating')->nullable();
            $table->text('driver_review')->nullable();

            $table->timestamps();

            $table->unique('booking_id'); // one review per booking
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
