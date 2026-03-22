<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->enum('billing_cycle', ['free', 'monthly', 'yearly'])->default('free');
            $table->decimal('price', 10, 2)->default(0);

            $table->unsignedInteger('max_vehicles')->default(2);
            $table->unsignedInteger('max_drivers')->default(2);

            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
