<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('driver_trip_logs')) {
            return;
        }

        Schema::create('driver_trip_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('grand_total_iqd', 15, 2)->default(0);
            $table->decimal('grand_total_usd', 15, 2)->default(0);
            $table->decimal('paid_iqd', 15, 2)->default(0);
            $table->decimal('paid_usd', 15, 2)->default(0);
            $table->decimal('remaining_iqd', 15, 2)->default(0);
            $table->decimal('remaining_usd', 15, 2)->default(0);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_trip_logs');
    }
};
