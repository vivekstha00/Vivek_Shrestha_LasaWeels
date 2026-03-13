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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            // Vendor = user with role 'vendor'
            $table->foreignId('vendor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('wheel_type')->nullable();
            $table->string('vehicle_type');
            $table->string('brand');
            $table->string('model');
            $table->string('variant')->nullable();

            $table->string('registration_no')->unique();
            $table->year('manufacture_year')->nullable();

            // Specs
            $table->string('fuel_type');
            $table->string('transmission');
            $table->unsignedTinyInteger('seating_capacity')->default(4);
            $table->decimal('mileage_per_litre', 5, 2)->nullable();
            $table->decimal('fuel_tank_capacity', 5, 2)->nullable();
            $table->decimal('battery_capacity', 6, 2)->nullable();
            $table->decimal('range_per_charge', 6, 2)->nullable();
            $table->decimal('charging_time', 5, 2)->nullable();
            $table->string('charger_type')->nullable();
            $table->string('currency', 3)->default('NPR');

            // Pricing
            $table->decimal('price_per_day', 10, 2);
            $table->decimal('with_driver_price_per_day', 10, 2)->nullable();
            $table->decimal('security_deposit', 10, 2)->default(0);

            // Location / pickup
            $table->string('location_city');
            $table->string('location_area')->nullable();
            $table->string('pickup_address')->nullable();

            $table->string('image_url')->nullable();

            $table->text('description')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('is_active')->default(true);

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('approved_at')->nullable();
            $table->text('reject_reason')->nullable();

            $table->timestamps();

            // Helpful indexes
            $table->index(['vendor_id', 'status']);
            $table->index(['location_city']);
            $table->index(['vehicle_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
