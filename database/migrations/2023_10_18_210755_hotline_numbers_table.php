<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotline_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('number');
            $table->string('logo')->nullable();
            $table->foreignId('user_id')->references('id')->on('user')->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotline_numbers');
    }
};
