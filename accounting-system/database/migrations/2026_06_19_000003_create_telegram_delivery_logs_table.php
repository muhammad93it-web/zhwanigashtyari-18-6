<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('telegram_delivery_logs')) {
            return;
        }

        Schema::create('telegram_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->string('content_type');
            $table->string('status');                 // success | failed
            $table->string('trigger')->default('schedule'); // schedule | manual
            $table->string('file_name')->nullable();
            $table->text('message')->nullable();      // error text or info
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_delivery_logs');
    }
};
