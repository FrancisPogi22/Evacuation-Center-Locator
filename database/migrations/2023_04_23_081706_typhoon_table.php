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
        Schema::create('typhoon', function (Blueprint $table) {
            $table->id("typhoon_id");
            $table->string("typhoon_name");
            $table->unsignedBigInteger("disaster_id");
            $table->foreign('disaster_id')->references('disaster_id')->on('disaster')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('typhoon');
    }
};
