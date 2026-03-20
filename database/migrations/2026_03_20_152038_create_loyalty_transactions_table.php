<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();

            $table->string('event_key')->unique();

            $table->enum('type', [
                'earn_booking',
                'earn_first_booking_bonus',
                'earn_review_bonus',
                'redeem',
                'restore_redemption',
                'manual_adjustment',
            ]);

            $table->integer('points');
            $table->decimal('amount_npr', 10, 2)->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
    }
};
