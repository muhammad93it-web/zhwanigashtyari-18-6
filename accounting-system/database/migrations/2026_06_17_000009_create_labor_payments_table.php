<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labor_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('worker_id')->nullable()->constrained()->nullOnDelete();
            $table->string('worker_name')->comment('ناوی کرێکار لە کاتی تۆمارکردن');
            $table->string('role')->nullable();
            $table->date('date');
            $table->boolean('is_hourly')->default(true)->comment('بەسەعات (سەعات×نرخ) یان بڕی جێگیر');
            $table->decimal('hours', 10, 2)->nullable();
            $table->decimal('hourly_rate', 15, 2)->nullable();
            $table->decimal('amount', 15, 2)->comment('کۆی پارەدان');
            $table->enum('currency', ['IQD', 'USD'])->default('IQD');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labor_payments');
    }
};
