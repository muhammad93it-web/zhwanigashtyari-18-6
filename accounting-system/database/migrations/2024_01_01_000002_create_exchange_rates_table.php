<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('usd_to_iqd', 12, 4)->comment('1 USD = X IQD');
            $table->string('notes')->nullable();
            $table->string('set_by')->nullable();
            $table->timestamp('effective_from')->useCurrent();
            $table->timestamps();

            $table->index('effective_from');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
