<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Idempotent: the columns may already exist if the SQL upgrade script
        // (jwani_upgrade_users.sql) was imported before migrations were run.
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('password');
            }
            if (! Schema::hasColumn('users', 'permissions')) {
                $table->text('permissions')->nullable()->after('is_admin');
            }
        });

        // Existing accounts keep full access so nothing breaks after upgrade.
        DB::table('users')->where('is_admin', false)->whereNull('permissions')->update(['is_admin' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['is_admin', 'permissions'],
                fn ($col) => Schema::hasColumn('users', $col)
            ));

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
