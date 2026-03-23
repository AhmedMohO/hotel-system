<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('national_id')->unique()->after('email');
        $table->string('avatar_image')->nullable()->after('national_id');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['national_id', 'avatar_image']);
    });
}
};
