<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // وەسڵی کڕین: ژمارەی وەسڵی هاتوو (دەستی)
        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_invoices', 'incoming_invoice_number')) {
                $table->string('incoming_invoice_number')->nullable()->after('id');
                $table->index('incoming_invoice_number');
            }
        });

        // کرێی کار: شێوازی پارەدان (جێگیر/کاتژمێری/ڕۆژانە) + ڕۆژ و کرێی ڕۆژانە
        Schema::table('labor_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('labor_payments', 'payment_mode')) {
                $table->string('payment_mode', 20)->default('hourly')->after('is_hourly');
            }
            if (! Schema::hasColumn('labor_payments', 'days')) {
                $table->decimal('days', 10, 2)->nullable()->after('hours');
            }
            if (! Schema::hasColumn('labor_payments', 'daily_rate')) {
                $table->decimal('daily_rate', 15, 2)->nullable()->after('hourly_rate');
            }
        });

        // Backfill payment_mode بۆ تۆمارە کۆنەکان لە is_hourly
        if (Schema::hasColumn('labor_payments', 'payment_mode')) {
            DB::table('labor_payments')->where('is_hourly', true)->update(['payment_mode' => 'hourly']);
            DB::table('labor_payments')->where('is_hourly', false)->update(['payment_mode' => 'fixed']);
        }
    }

    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_invoices', 'incoming_invoice_number')) {
                $table->dropIndex(['incoming_invoice_number']);
                $table->dropColumn('incoming_invoice_number');
            }
        });

        Schema::table('labor_payments', function (Blueprint $table) {
            foreach (['payment_mode', 'days', 'daily_rate'] as $col) {
                if (Schema::hasColumn('labor_payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
