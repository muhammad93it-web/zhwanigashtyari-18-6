<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('type', ['sale', 'purchase', 'debit', 'credit'])
                ->comment('sale=فرۆشتن, purchase=کڕین, debit=قەرز/بردراو, credit=دانەوەی قەرز/هێنراو');

            $table->enum('currency', ['USD', 'IQD'])->default('USD');
            $table->decimal('amount', 15, 2)->comment('Original entered amount');

            // Both currencies always stored — computed at creation from locked rate
            $table->decimal('amount_usd', 15, 4);
            $table->decimal('amount_iqd', 15, 2);

            // IMMUTABLE: exchange rate locked at time of transaction — NEVER updated after save
            $table->decimal('exchange_rate_usd_to_iqd', 12, 4)
                ->comment('Rate locked at transaction creation. Historical accuracy preserved.');

            $table->string('description');
            $table->string('reference_number', 50)->unique();
            $table->date('transaction_date');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['client_id', 'transaction_date']);
            $table->index(['type', 'transaction_date']);
            $table->index('transaction_date');
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
