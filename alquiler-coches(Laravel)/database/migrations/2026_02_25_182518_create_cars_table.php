<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price_per_day', 8, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    function down(): void {
        Schema::dropIfExists('cars');
    }
};
