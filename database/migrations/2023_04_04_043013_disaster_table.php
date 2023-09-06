<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('disaster', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->foreignId('user_id')->references('id')->on('user')->cascadeOnUpdate();
            $table->boolean('is_archive');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disaster');
    }
};
