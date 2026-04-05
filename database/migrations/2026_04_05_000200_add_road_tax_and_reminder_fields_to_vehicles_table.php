<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('road_tax_document_path')->nullable()->after('insurance_expiry_date');
            $table->date('road_tax_expiry_date')->nullable()->after('road_tax_document_path');
            $table->date('insurance_expiry_reminder_sent_on')->nullable()->after('road_tax_expiry_date');
            $table->date('road_tax_expiry_reminder_sent_on')->nullable()->after('insurance_expiry_reminder_sent_on');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'road_tax_document_path',
                'road_tax_expiry_date',
                'insurance_expiry_reminder_sent_on',
                'road_tax_expiry_reminder_sent_on',
            ]);
        });
    }
};
