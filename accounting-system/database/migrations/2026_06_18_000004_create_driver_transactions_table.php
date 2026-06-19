<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('driver_transactions')) {
            return;
        }

        Schema::create('driver_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->restrictOnDelete();
            $table->foreignId('driver_trip_log_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('expense_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['trip', 'payment', 'adjustment'])->comment('trip=کرێی گواستنەوە (قەرز زیاد), payment=پارەدان (قەرز کەم)');
            $table->enum('currency', ['IQD', 'USD'])->default('IQD');
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2)->comment('باڵانسی شۆفێر دوای ئەم مامەڵەیە');
            $table->date('date');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_transactions');
    }
};
