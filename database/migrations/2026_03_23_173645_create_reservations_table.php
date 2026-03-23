<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('reservations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
        $table->foreignId('room_id')->constrained('rooms')->restrictOnDelete();
        $table->unsignedInteger('accompany_number');
        $table->unsignedBigInteger('paid_price');
        $table->foreignId('approved_by')->constrained('users')->restrictOnDelete();
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }

};
