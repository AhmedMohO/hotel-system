<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Drop old FK before changing nullability
            $table->dropForeign(['approved_by']);

            $table->foreignId('approved_by')->nullable()->change();

            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);

            $table->foreignId('approved_by')->nullable(false)->change();

            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });
    }
};
