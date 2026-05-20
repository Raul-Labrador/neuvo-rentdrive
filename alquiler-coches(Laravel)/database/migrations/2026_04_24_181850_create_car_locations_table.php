<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::create('car_locations', function (Blueprint $table) {
            $table->id();
            // Relación con el coche
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            // Coordenadas con alta precisión
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            $table->timestamps();
        });
    }

    function down(): void {
        Schema::dropIfExists('car_locations');
    }
};
