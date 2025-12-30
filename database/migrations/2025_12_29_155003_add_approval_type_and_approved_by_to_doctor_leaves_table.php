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
        Schema::table('doctor_leaves', function (Blueprint $table) {
            if (!Schema::hasColumn('doctor_leaves', 'approval_type')) {
                $table->enum('approval_type', ['auto', 'admin'])->default('admin')->after('doctor_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_leaves', function (Blueprint $table) {
            //
        });
    }
};
