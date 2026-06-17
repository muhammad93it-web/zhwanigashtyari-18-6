<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index()->comment('ناوی دابینکەر');
            $table->string('phone')->nullable()->comment('ژمارەی تەلەفۆن');
            $table->decimal('balance', 15, 2)->default(0)->comment('باڵانس: ئەوەی قەرزاریانین (+) یان پێشمان داوە (-)');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
