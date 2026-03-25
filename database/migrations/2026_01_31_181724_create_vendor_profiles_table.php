<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            // personal info
            $table->string('full_name');
            $table->string('phone');
            $table->string('national_id_number');
            $table->text('residential_address');

            // business info
            $table->string('business_name');
            $table->string('business_type');
            $table->string('business_registration_number')->nullable();
            $table->string('tax_id_number')->nullable();
            $table->text('business_address');

            // workflow
            $table->unsignedTinyInteger('current_step')->default(1);
            $table->boolean('is_submitted')->default(false);

            $table->string('status')->default('draft');
            // draft, pending, under_review, approved, rejected

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_profiles');
    }
};
