<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('subscription_plan_id')
                ->constrained('subscription_plans')
                ->cascadeOnDelete();

            $table->decimal('amount', 10, 2);

            $table->string('payment_gateway', 30)->default('khalti');
            $table->string('purchase_order_id')->nullable()->unique();

            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');

            $table->string('gateway_reference')->nullable();
            $table->json('gateway_payload')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['vendor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
