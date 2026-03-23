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

            $table->text('special_request')->nullable();

            $table->enum('status', [
                'pending',
                'confirmed',
                'active',
                'completed',
                'cancel_requested',
                'cancelled'
            ])->default('pending');

            $table->enum('payment_status', [
                'unpaid',
                'partial',
                'paid'
            ])->default('unpaid');

            $table->decimal('original_price', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->enum('discount_type', ['none', 'loyalty', 'code'])->default('none');
            $table->string('discount_code')->nullable();

            $table->decimal('total_price', 10, 2)->default(0);

            $table->unsignedInteger('loyalty_points_earned')->default(0);
            $table->unsignedInteger('loyalty_points_redeemed')->default(0);
            $table->decimal('loyalty_discount_amount', 10, 2)->default(0);

            $table->decimal('security_deposit', 10, 2)->nullable();

            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('loyalty_processed_at')->nullable();

            $table->timestamp('cancellation_requested_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();

            $table->index(['vehicle_id', 'pickup_datetime', 'drop_datetime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
