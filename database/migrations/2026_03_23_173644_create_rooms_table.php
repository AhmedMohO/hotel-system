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
    Schema::create('rooms', function (Blueprint $table) {
        $table->id();
        $table->string('number')->unique();
        $table->foreignId('floor_id')->constrained('floors')->restrictOnDelete();
        $table->unsignedInteger('capacity');
        $table->unsignedBigInteger('price');
        $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
