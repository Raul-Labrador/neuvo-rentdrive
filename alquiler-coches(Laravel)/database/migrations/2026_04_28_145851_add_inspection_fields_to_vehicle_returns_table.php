<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::table('vehicle_returns', function (Blueprint $table) {
            $table->boolean('needs_review')->default(false)->after('damages');
            $table->boolean('needs_workshop')->default(false)->after('needs_review');
            $table->string('final_car_status')->nullable()->after('needs_workshop');
        });
    }

    function down(): void {
        Schema::table('vehicle_returns', function (Blueprint $table) {
            $table->dropColumn(['needs_review', 'needs_workshop', 'final_car_status']);
        });
    }
};
