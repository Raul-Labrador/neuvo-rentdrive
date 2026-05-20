<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::create('incident_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('incidents')->cascadeOnDelete();
            $table->string('path');
            $table->string('type')->default('general');
            $table->timestamps();
        });
    }

    function down(): void {
        Schema::dropIfExists('incident_images');
    }
};
