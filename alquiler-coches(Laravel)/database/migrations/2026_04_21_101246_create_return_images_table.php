<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::create('return_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_return_id')->constrained()->cascadeOnDelete();

            $table->string('path');
            $table->string('type')->nullable(); // damage, general, interior, etc.

            $table->timestamps();
        });
    }

    function down(): void {
        Schema::dropIfExists('return_images');
    }
};
