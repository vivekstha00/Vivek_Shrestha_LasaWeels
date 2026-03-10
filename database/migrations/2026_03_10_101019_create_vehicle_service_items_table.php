<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_service_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_service_id')
                ->constrained('vehicle_services')
                ->cascadeOnDelete();

            $table->string('service_item');
            $table->boolean('is_custom')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_service_items');
    }
};
