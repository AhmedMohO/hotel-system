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
    Schema::create('clients', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->string('avatar_image')->nullable();
        $table->string('mobile');
        $table->string('country');
        $table->enum('gender', ['Male', 'Female']);
        $table->foreignId('approved_by')->nullable()
              ->constrained('users')->nullOnDelete();
        $table->timestamp('approved_at')->nullable();
        $table->timestamp('last_login_at')->nullable();
        $table->rememberToken();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('clients');
}

};
