<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_update', function (Blueprint $table) {
            $table->id();
            $table->string('update_time');
            $table->string('update_details');
            $table->foreignId('report_id')->references('id')->on('resident_report')->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_update');
    }
};
