<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->boolean('status')->default(1);
            $table->tinyInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_categories');
    }
};
