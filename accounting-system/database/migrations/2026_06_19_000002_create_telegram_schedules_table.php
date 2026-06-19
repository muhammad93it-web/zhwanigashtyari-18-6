<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('telegram_schedules')) {
            return;
        }

        Schema::create('telegram_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('content_type');           // backup | daily_report | monthly_report | transactions
            $table->string('frequency');              // daily | monthly
            $table->unsignedTinyInteger('day_of_month')->nullable(); // for monthly (1..31)
            $table->string('send_time', 5);           // HH:MM
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_schedules');
    }
};
