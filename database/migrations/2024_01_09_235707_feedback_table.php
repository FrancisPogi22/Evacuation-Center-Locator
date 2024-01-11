<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->boolean('clean_facilities');
            $table->boolean('responsive_aid');
            $table->boolean('safe_evacutaion');
            $table->boolean('sufficient_food_supply');
            $table->boolean('comfortable_evacuation');
            $table->boolean('well_managed_evacuation');
            $table->foreignId('evacuation_center_id')->references('id')->on('evacuation_center')->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
