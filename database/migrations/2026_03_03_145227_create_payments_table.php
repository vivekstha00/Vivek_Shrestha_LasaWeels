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

            $table->unsignedBigInteger('vendor_id')->nullable();

            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();

            $table->decimal('amount', 10, 2);

            $table->string('method', 30);

            $table->string('status', 30)->default('pending');

            $table->decimal('platform_commission', 10, 2)->default(0);
            $table->decimal('vendor_amount', 10, 2)->default(0);

            $table->string('payout_status', 30)->default('unpaid');

            $table->string('gateway_reference')->nullable();  
            $table->json('gateway_payload')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
