<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses', 'project_id')) {
                $table->foreignId('project_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('expenses', 'expense_type')) {
                $table->string('expense_type')->nullable()->after('payee')->comment('جۆری خەرجی بە دەست، نموونە: تەکسی، پارەی وەستا، خواردن');
            }
            if (! Schema::hasColumn('expenses', 'reason_description')) {
                $table->text('reason_description')->nullable()->after('description')->comment('هۆکار/وردەکاری زیاتر');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'project_id')) {
                $table->dropConstrainedForeignId('project_id');
            }
            if (Schema::hasColumn('expenses', 'expense_type')) {
                $table->dropColumn('expense_type');
            }
            if (Schema::hasColumn('expenses', 'reason_description')) {
                $table->dropColumn('reason_description');
            }
        });
    }
};
