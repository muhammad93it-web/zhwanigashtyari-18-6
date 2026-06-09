<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('party_name')->comment('ناوی کەس/لایەنی قەرزدار');
            $table->enum('direction', ['receivable', 'payable'])
                ->comment('receivable=قەرزی لای خەڵک (بۆ ئێمە), payable=قەرزی ئێمە (لەسەر ئێمە)');
            $table->enum('currency', ['USD', 'IQD'])->default('IQD');
            $table->decimal('amount', 15, 2);
            $table->decimal('amount_usd', 15, 4);
            $table->decimal('amount_iqd', 15, 2);
            $table->decimal('exchange_rate_usd_to_iqd', 12, 4);
            $table->enum('status', ['open', 'paid'])->default('open');
            $table->string('description')->nullable();
            $table->string('reference_number', 50)->unique();
            $table->date('debt_date');
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['direction', 'status']);
            $table->index('debt_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
