<?php
namespace Illuminate\Database\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')
                ->constrained('vehicles')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('drivers')
                ->nullOnDelete();

            $table->enum('service', ['self', 'driver'])->default('self');

            $table->string('pickup_location');
            $table->string('drop_location');

            $table->dateTime('pickup_datetime');
            $table->dateTime('drop_datetime');

            // NEW: special request
            $table->text('special_request')->nullable();

            // Booking lifecycle
            $table->enum('status', [
                'pending',     // created but not paid
                'confirmed',   // paid & approved
                'active',      // trip started
                'completed',   // trip finished
                'cancelled'
            ])->default('pending');

            // Payment tracking
            $table->enum('payment_status', [
                'unpaid',
                'partial',
                'paid'
            ])->default('unpaid');

            $table->decimal('total_price', 10, 2)->default(0);

            $table->decimal('security_deposit', 10, 2)->nullable();

            $table->timestamp('reminder_sent_at')->nullable();

            $table->timestamps();

            $table->index(['vehicle_id', 'pickup_datetime', 'drop_datetime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
