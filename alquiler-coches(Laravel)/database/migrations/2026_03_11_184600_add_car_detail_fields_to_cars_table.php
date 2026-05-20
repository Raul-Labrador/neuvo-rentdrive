<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('name');
            $table->string('model')->nullable()->after('brand');
            $table->year('year')->nullable()->after('model');
            $table->decimal('price', 10, 2)->nullable()->after('year');
            $table->string('fuel')->nullable()->after('price');
            $table->integer('km')->nullable()->after('fuel');
            $table->string('transmission')->nullable()->after('km');
            $table->string('engine_displacement')->nullable()->after('transmission');
            $table->integer('horsepower')->nullable()->after('engine_displacement');
            $table->string('emissions')->nullable()->after('horsepower');
            $table->integer('doors')->nullable()->after('emissions');
            $table->integer('seats')->nullable()->after('doors');
            $table->string('body')->nullable()->after('seats');
            $table->string('trunk')->nullable()->after('body');
            $table->string('color')->nullable()->after('trunk');
        });
    }

    function down(): void {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn([
                'brand', 'model', 'year', 'price', 'fuel', 'km',
                'transmission', 'engine_displacement', 'horsepower',
                'emissions', 'doors', 'seats', 'body', 'trunk', 'color',
            ]);
        });
    }
};
