<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->unsignedInteger('available_points')->default(0);
            $table->unsignedInteger('lifetime_earned_points')->default(0);
            $table->unsignedInteger('lifetime_redeemed_points')->default(0);

            $table->enum('tier', ['bronze', 'silver', 'gold'])->default('bronze');

            $table->unsignedInteger('completed_bookings_count')->default(0);
            $table->decimal('yearly_spend', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_accounts');
    }
};
