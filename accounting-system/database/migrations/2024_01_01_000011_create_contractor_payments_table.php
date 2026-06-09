<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractor_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('currency', ['USD', 'IQD'])->default('IQD');
            $table->decimal('amount', 15, 2);
            $table->decimal('amount_usd', 15, 4);
            $table->decimal('amount_iqd', 15, 2);
            $table->decimal('exchange_rate_usd_to_iqd', 12, 4);

            $table->decimal('meters', 15, 3)->nullable()->comment('ژمارەی مەتر — ئەگەر بەمەتر بێت');
            $table->string('description')->nullable();
            $table->string('reference_number', 50)->unique();
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['contractor_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_payments');
    }
};
