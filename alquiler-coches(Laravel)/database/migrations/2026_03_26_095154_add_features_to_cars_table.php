<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::table('cars', function (Blueprint $table) {
            $table->text('features')->nullable()->after('color');
        });
    }

    function down(): void {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('features');
        });
    }
};
