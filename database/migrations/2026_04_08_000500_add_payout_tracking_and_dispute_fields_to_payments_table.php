<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('dispute_status', 30)->default('none')->after('refund_status');
            $table->timestamp('paid_out_at')->nullable()->after('payout_status');
            $table->foreignId('paid_out_by')->nullable()->after('paid_out_at')->constrained('users')->nullOnDelete();

            $table->index('dispute_status');
            $table->index(['vendor_id', 'payout_status']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('paid_out_by');
            $table->dropIndex(['vendor_id', 'payout_status']);
            $table->dropIndex(['dispute_status']);

            $table->dropColumn([
                'dispute_status',
                'paid_out_at',
            ]);
        });
    }
};
