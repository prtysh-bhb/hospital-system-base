<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('day_of_week'); // 0=Sunday, 1=Monday, ... 6=Saturday
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('slot_duration')->default(30);
            $table->unsignedSmallInteger('max_patients')->default(20);
            $table->boolean('is_available')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['doctor_id', 'day_of_week']);
            $table->index(['doctor_id', 'is_available']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
    }
};
