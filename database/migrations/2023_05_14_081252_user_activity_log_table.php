<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('user')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('activity');
            $table->string('date_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
