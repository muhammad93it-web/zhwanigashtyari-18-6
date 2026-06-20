<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('letters')) {
            Schema::create('letters', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('reference_number');
                $table->date('letter_date');
                $table->string('recipient')->nullable();
                $table->string('subject')->nullable();
                $table->text('body')->nullable();
                $table->timestamps();

                $table->index('reference_number');
                $table->index('letter_date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
