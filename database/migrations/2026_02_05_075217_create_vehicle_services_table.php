<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();

            $table->date('service_date');
            $table->string('service_type'); // oil_change, general_service, etc.
            $table->unsignedInteger('odometer_km')->nullable();
            $table->unsignedInteger('cost')->nullable();
            $table->text('notes')->nullable();

            $table->date('next_service_due_date')->nullable();
            $table->unsignedInteger('next_service_due_km')->nullable();
            $table->index(['vehicle_id', 'service_date']);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_services');
    }
};
