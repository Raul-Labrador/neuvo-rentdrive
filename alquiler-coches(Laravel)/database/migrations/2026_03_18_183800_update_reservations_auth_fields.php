<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'name')) {
                $table->renameColumn('name', 'customer_name');
            }
            if (Schema::hasColumn('reservations', 'email')) {
                $table->renameColumn('email', 'customer_email');
            }
            if (!Schema::hasColumn('reservations', 'wp_user_id')) {
                $table->unsignedBigInteger('wp_user_id')->nullable()->after('customer_email');
            }
        });
    }

    function down(): void {
        Schema::table('reservations', function (Blueprint $table) {
            $table->renameColumn('customer_name', 'name');
            $table->renameColumn('customer_email', 'email');
            $table->dropColumn('wp_user_id');
        });
    }
};
