<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_invoices', 'deliverer_name')) {
                $table->string('deliverer_name')->nullable()->after('supplier_id')->comment('ناوی کەسی گەیەنەری مەواد');
            }
            if (! Schema::hasColumn('purchase_invoices', 'deliverer_phone')) {
                $table->string('deliverer_phone')->nullable()->after('deliverer_name');
            }
            if (! Schema::hasColumn('purchase_invoices', 'deliverer_address')) {
                $table->string('deliverer_address')->nullable()->after('deliverer_phone');
            }
            if (! Schema::hasColumn('purchase_invoices', 'vehicle_number')) {
                $table->string('vehicle_number')->nullable()->after('deliverer_address')->comment('ژمارەی ئۆتۆمبێل');
            }
            if (! Schema::hasColumn('purchase_invoices', 'vehicle_type')) {
                $table->string('vehicle_type')->nullable()->after('vehicle_number')->comment('جۆری ئۆتۆمبێل');
            }
            if (! Schema::hasColumn('purchase_invoices', 'project_id')) {
                $table->foreignId('project_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            }
            foreach (['total_iqd', 'total_usd', 'paid_iqd', 'paid_usd', 'remaining_iqd', 'remaining_usd'] as $col) {
                if (! Schema::hasColumn('purchase_invoices', $col)) {
                    $table->decimal($col, 15, 2)->default(0);
                }
            }
        });

        // Make supplier_id nullable (ad-hoc deliverers without a tracked account). FK-safe raw ALTER.
        DB::statement('ALTER TABLE `purchase_invoices` MODIFY `supplier_id` BIGINT UNSIGNED NULL');

        Schema::table('purchase_invoice_details', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_invoice_details', 'currency')) {
                $table->enum('currency', ['IQD', 'USD'])->default('IQD')->after('line_total');
            }
        });

        Schema::table('suppliers', function (Blueprint $table) {
            if (! Schema::hasColumn('suppliers', 'balance_iqd')) {
                $table->decimal('balance_iqd', 15, 2)->default(0)->after('balance');
            }
            if (! Schema::hasColumn('suppliers', 'balance_usd')) {
                $table->decimal('balance_usd', 15, 2)->default(0)->after('balance_iqd');
            }
        });

        Schema::table('supplier_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('supplier_transactions', 'currency')) {
                $table->enum('currency', ['IQD', 'USD'])->default('IQD')->after('type');
            }
        });
    }

    public function down(): void
    {
        // Non-destructive upgrade: leave columns in place to preserve data.
    }
};
