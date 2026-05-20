<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::create('vehicle_returns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();

            $table->integer('km_returned');
            $table->boolean('is_clean')->default(true);

            $table->text('notes')->nullable();
            $table->text('damages')->nullable();

            $table->timestamp('returned_at')->nullable();

            $table->timestamps();
        });
    }

    function down(): void {
        Schema::dropIfExists('vehicle_returns');
    }
};
