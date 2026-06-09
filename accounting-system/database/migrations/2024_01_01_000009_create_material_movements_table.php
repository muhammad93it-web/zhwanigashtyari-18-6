<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('type', ['purchase', 'sale'])->comment('purchase=کڕین, sale=فرۆشتن');
            $table->decimal('quantity', 15, 3);
            $table->decimal('unit_price', 15, 2)->comment('نرخی یەک یەکە');

            $table->enum('currency', ['USD', 'IQD'])->default('IQD');
            $table->decimal('amount', 15, 2)->comment('کۆی گشتی = quantity * unit_price');
            $table->decimal('amount_usd', 15, 4);
            $table->decimal('amount_iqd', 15, 2);
            $table->decimal('exchange_rate_usd_to_iqd', 12, 4);

            $table->string('party_name')->nullable()->comment('دابینکەر یان کڕیار');
            $table->string('reference_number', 50)->unique();
            $table->date('movement_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['type', 'movement_date']);
            $table->index(['material_id', 'movement_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_movements');
    }
};
