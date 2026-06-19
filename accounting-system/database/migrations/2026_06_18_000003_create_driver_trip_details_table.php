<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('driver_trip_details')) {
            return;
        }

        Schema::create('driver_trip_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_trip_log_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('work_type')->comment('waste_disposal=گواستنەوەی خۆڵ, sub_base=سەب بەیس');
            $table->decimal('trip_count', 15, 2)->default(0);
            $table->decimal('price_per_trip', 15, 2)->default(0);
            $table->enum('currency', ['IQD', 'USD'])->default('IQD');
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_trip_details');
    }
};
