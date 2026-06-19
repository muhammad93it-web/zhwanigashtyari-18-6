<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('expenses') || Schema::hasColumn('expenses', 'driver_trip_log_id')) {
            return;
        }

        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_trip_log_id')->nullable()->after('project_id');
            $table->index('driver_trip_log_id');
        });

        // SQLite cannot add a foreign key to an existing table via ALTER (and does
        // not enforce FKs by default), so only add the constraint on MySQL. This
        // keeps the Laravel migration path in lockstep with jwani_database_setup.sql
        // and jwani_upgrade_construction.sql, which both add this FK (ON DELETE SET NULL).
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('driver_trip_logs')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->foreign('driver_trip_log_id')
                    ->references('id')->on('driver_trip_logs')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'driver_trip_log_id')) {
            if (DB::getDriverName() === 'mysql') {
                Schema::table('expenses', function (Blueprint $table) {
                    $table->dropForeign(['driver_trip_log_id']);
                });
            }

            Schema::table('expenses', function (Blueprint $table) {
                $table->dropIndex(['driver_trip_log_id']);
                $table->dropColumn('driver_trip_log_id');
            });
        }
    }
};
