<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // FK to vehicles.id (Laravel default)
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('service', ['self', 'driver'])->default('self');

            $table->string('pickup_location');
            $table->string('drop_location');

            $table->dateTime('pickup_datetime');
            $table->dateTime('drop_datetime');

            $table->enum('status', ['pending','confirmed','completed','cancelled'])->default('pending');

            $table->decimal('total_price', 10, 2)->default(0);

            $table->timestamps();

            $table->index(['vehicle_id', 'pickup_datetime', 'drop_datetime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
