<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();

            $table->decimal('amount', 10, 2);

            $table->string('method', 30);

            $table->string('payment_type', 30)->default('full_online');

            $table->decimal('paid_amount', 10, 2)->default(0);

            $table->decimal('remaining_amount', 10, 2)->default(0);

            $table->decimal('deposit_amount', 10, 2)->default(0);

            $table->string('status', 30)->default('pending');

            $table->string('deposit_status', 30)->nullable();

            $table->string('settlement_status', 50)->nullable();

            $table->decimal('platform_commission', 10, 2)->default(0);

            $table->decimal('vendor_amount', 10, 2)->default(0);

            $table->string('payout_status', 30)->default('unpaid');

            $table->string('gateway_reference')->nullable();

            $table->json('gateway_payload')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->enum('refund_status', ['none', 'pending', 'refunded', 'rejected'])->default('none');
            $table->timestamp('refund_requested_at')->nullable();
            $table->timestamp('refund_processed_at')->nullable();
            $table->text('refund_note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
