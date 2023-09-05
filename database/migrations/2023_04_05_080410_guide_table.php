<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guide', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->longText('content');
            $table->foreignId('guideline_id')->references('id')->on('guideline')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('guide_photo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guide');
    }
};
