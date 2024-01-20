<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disaster_damage', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->integer('quantity');
            $table->integer('cost');
            $table->string('barangay');
            $table->foreignId('disaster_id')->references('id')->on('disaster')->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disaster_damage');
    }
};
