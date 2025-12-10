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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['appointment_reminder', 'appointment_confirmed', 'appointment_cancelled', 'prescription_ready', 'system_alert'])->index();
            $table->string('title');
            $table->text('message');
            $table->string('related_type', 50)->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->enum('channel', ['database', 'email', 'sms', 'push'])->default('database');
            $table->boolean('is_read')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
