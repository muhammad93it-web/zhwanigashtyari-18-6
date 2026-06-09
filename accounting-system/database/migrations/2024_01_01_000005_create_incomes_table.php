<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source')->comment('سەرچاوەی پارەکە / لە کێوە');
            $table->string('category')->nullable();
            $table->enum('currency', ['USD', 'IQD'])->default('IQD');
            $table->decimal('amount', 15, 2);
            $table->decimal('amount_usd', 15, 4);
            $table->decimal('amount_iqd', 15, 2);
            $table->decimal('exchange_rate_usd_to_iqd', 12, 4);
            $table->string('description')->nullable();
            $table->string('reference_number', 50)->unique();
            $table->date('income_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('income_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
