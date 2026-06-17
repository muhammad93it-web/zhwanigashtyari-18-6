<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index()->comment('ناوی کرێکار/وەستا');
            $table->string('role')->nullable()->comment('جۆر: وەستا، کرێکار، چاودێر، شۆفێر، شۆڤڵ...');
            $table->string('phone')->nullable();
            $table->decimal('default_hourly_rate', 15, 2)->nullable()->comment('نرخی سەعاتی بنەڕەت');
            $table->enum('default_currency', ['IQD', 'USD'])->default('IQD');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
