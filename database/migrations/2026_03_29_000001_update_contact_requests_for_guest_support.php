<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('contact_requests', 'email')) {
                $table->string('email')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('contact_requests', 'phone')) {
                $table->string('phone', 30)->nullable()->after('email');
            }
        });

        Schema::table('contact_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contact_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            if (Schema::hasColumn('contact_requests', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('contact_requests', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
