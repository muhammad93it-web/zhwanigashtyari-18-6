<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('phone', 50)->nullable();
            $table->enum('work_type', ['per_meter', 'contract'])
                ->comment('per_meter=بە مەتر, contract=قۆنتەرات');
            $table->decimal('rate_per_meter', 15, 2)->nullable()->comment('نرخی مەتر — بۆ جۆری بەمەتر');
            $table->decimal('contract_amount', 15, 2)->nullable()->comment('بڕی گشتیی قۆنتەرات');
            $table->enum('currency', ['USD', 'IQD'])->default('IQD');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractors');
    }
};
