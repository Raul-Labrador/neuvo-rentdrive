<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('car_id')->constrained('cars')->cascadeOnDelete();
            $table->unsignedBigInteger('wp_user_id');
            $table->string('type');
            $table->text('description');
            $table->string('status')->default('open');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    function down(): void {
        Schema::dropIfExists('incidents');
    }
};
