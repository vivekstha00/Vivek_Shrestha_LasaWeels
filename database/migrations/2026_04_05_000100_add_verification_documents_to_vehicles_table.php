<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('vehicle_registration_document_path')->nullable()->after('image_url');
            $table->string('insurance_document_path')->nullable()->after('vehicle_registration_document_path');
            $table->date('insurance_expiry_date')->nullable()->after('insurance_document_path');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'vehicle_registration_document_path',
                'insurance_document_path',
                'insurance_expiry_date',
            ]);
        });
    }
};
