<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('drivers')) {
            return;
        }

        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->decimal('balance', 15, 2)->default(0)->comment('باڵانسی کۆن (دینار) بۆ گونجان');
            $table->decimal('balance_iqd', 15, 2)->default(0);
            $table->decimal('balance_usd', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('name');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
