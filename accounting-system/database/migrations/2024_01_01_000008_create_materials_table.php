<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('unit')->default('دانە')->comment('یەکەی پێوانە: مەتر، دانە، کیلۆ، کیس...');
            $table->string('category')->nullable();
            $table->decimal('current_stock', 15, 3)->default(0)->comment('بڕی ئێستای کۆگا');
            $table->decimal('min_stock', 15, 3)->nullable()->comment('کەمترین بڕی ئاگادارکردنەوە');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
