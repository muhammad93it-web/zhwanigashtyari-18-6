<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['purchase', 'payment'])->comment('purchase=کڕین (قەرز زیاد), payment=پارەدان (قەرز کەم)');
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2)->comment('باڵانسی دابینکەر دوای ئەم مامەڵەیە');
            $table->date('date');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_transactions');
    }
};
