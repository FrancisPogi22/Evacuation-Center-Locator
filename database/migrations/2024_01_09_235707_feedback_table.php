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
            $table->boolean('clean_facilities')->default(0);
            $table->boolean('responsive_aid')->default(0);
            $table->boolean('safe_evacuation')->default(0);
            $table->boolean('sufficient_food_supply')->default(0);
            $table->boolean('comfortable_evacuation')->default(0);
            $table->boolean('well_managed_evacuation')->default(0);
            $table->foreignId('evacuation_center_id')->references('id')->on('evacuation_center')->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
