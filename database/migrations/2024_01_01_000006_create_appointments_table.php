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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number', 20)->unique();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->unsignedSmallInteger('duration_minutes')->default(30);
            $table->enum('status', ['pending', 'confirmed', 'checked_in', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('pending')->index();
            $table->enum('appointment_type', ['consultation', 'follow_up', 'emergency', 'check_up'])->default('consultation');
            $table->text('reason_for_visit')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('booked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('booked_via', ['online', 'frontdesk', 'phone', 'admin'])->default('online');
            $table->boolean('reminder_sent')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['appointment_date', 'appointment_time']);
            $table->index(['patient_id', 'appointment_date']);
            $table->index(['doctor_id', 'appointment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
