<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_invoice_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained()->nullOnDelete();
            $table->string('custom_type')->nullable()->comment('جۆری دەستی، نموونە: تاسلوجە');
            $table->string('unit')->nullable()->comment('یەکە، نموونە: تەن');
            $table->decimal('quantity', 15, 3)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete()->comment('پڕۆژە/بینا — پێویستە ئەگەر خەرجی ڕاستەوخۆ بێت');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_invoice_details');
    }
};
