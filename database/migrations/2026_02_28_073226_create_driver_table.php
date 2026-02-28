<?php
namespace Illuminate\Database\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable()->unique();
            $table->string('license_number')->unique();
            $table->enum('availability_status', ['available', 'unavailable'])->default('available');
            $table->decimal('rating', 2, 1)->nullable();
            $table->enum('status', ['approved', 'removed'])->default('approved');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
