<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('reminder_7d_sent_at')->nullable()->after('reminder_sent_at');
            $table->timestamp('reminder_2h_sent_at')->nullable()->after('reminder_7d_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['reminder_7d_sent_at', 'reminder_2h_sent_at']);
        });
    }
};
