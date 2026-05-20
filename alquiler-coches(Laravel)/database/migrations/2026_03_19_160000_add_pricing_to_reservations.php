<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'total_price')) {
                $table->decimal('total_price', 10, 2)->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('reservations', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('status');
            }
        });
    }

    function down(): void {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['total_price', 'payment_status']);
        });
    }
};
