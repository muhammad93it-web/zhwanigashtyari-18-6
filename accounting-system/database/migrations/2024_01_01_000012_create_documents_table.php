<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('doc_type')->nullable()->comment('جۆری نووسراو');
            $table->string('reference_number', 50)->nullable();
            $table->string('recipient')->nullable()->comment('بۆ کێ');
            $table->longText('body')->nullable()->comment('ناوەڕۆکی نووسراو');
            $table->date('doc_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('doc_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
