<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payee')->comment('پارەکە بۆ کێ خەرجکرا');
            $table->string('category')->nullable();
            $table->enum('currency', ['USD', 'IQD'])->default('IQD');
            $table->decimal('amount', 15, 2);
            $table->decimal('amount_usd', 15, 4);
            $table->decimal('amount_iqd', 15, 2);
            $table->decimal('exchange_rate_usd_to_iqd', 12, 4);
            $table->string('description')->nullable();
            $table->string('reference_number', 50)->unique();
            $table->date('expense_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('expense_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
