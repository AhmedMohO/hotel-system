<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('reservations', 'status')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->string('status', 20)->default('pending')->after('approved_by');
            });
        }

        if (! $this->hasIndex('reservations', 'reservations_status_index')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->index('status');
            });
        }

        DB::table('reservations')
            ->whereNull('approved_by')
            ->update(['status' => 'pending']);

        DB::table('reservations')
            ->whereNotNull('approved_by')
            ->update(['status' => 'approved']);
    }

    public function down(): void
    {
        if ($this->hasIndex('reservations', 'reservations_status_index')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropIndex(['status']);
            });
        }

        if (Schema::hasColumn('reservations', 'status')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $driver = DB::getDriverName();

        return match ($driver) {
            'sqlite' => collect(DB::select("PRAGMA index_list('{$table}')"))
                ->contains(fn (object $index): bool => $index->name === $indexName),
            'mysql' => DB::table('information_schema.statistics')
                ->where('table_schema', DB::getDatabaseName())
                ->where('table_name', $table)
                ->where('index_name', $indexName)
                ->exists(),
            'pgsql' => DB::table('pg_indexes')
                ->where('schemaname', 'public')
                ->where('tablename', $table)
                ->where('indexname', $indexName)
                ->exists(),
            default => false,
        };
    }
};
