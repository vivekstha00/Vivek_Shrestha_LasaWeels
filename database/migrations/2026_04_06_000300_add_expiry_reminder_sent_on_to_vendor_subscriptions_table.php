<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_subscriptions', function (Blueprint $table) {
            $table->date('expiry_reminder_sent_on')
                ->nullable()
                ->after('ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('vendor_subscriptions', function (Blueprint $table) {
            $table->dropColumn('expiry_reminder_sent_on');
        });
    }
};
