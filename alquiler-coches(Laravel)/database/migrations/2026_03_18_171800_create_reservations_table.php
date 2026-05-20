<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('cars')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('name');
            $table->string('email');
            $table->string('status')->default('confirmed');
            $table->timestamps();
        });
    }

    function down(): void {
        Schema::dropIfExists('reservations');
    }
};
