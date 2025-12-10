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
        Schema::create('doctor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->foreignId('specialty_id')->nullable()->constrained()->onDelete('set null');
            $table->string('qualification')->nullable();
            $table->unsignedSmallInteger('experience_years')->default(0);
            $table->decimal('consultation_fee', 10, 2)->default(0.00);
            $table->text('bio')->nullable();
            $table->string('license_number', 50)->nullable();
            $table->boolean('available_for_booking')->default(true);
            $table->timestamps();
            $table->index('specialty_id');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_profiles');
    }
};
