<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hazard_report', function (Blueprint $table) {
            $table->id();
            $table->string('latitude');
            $table->string('longitude');
            $table->string('type');
            $table->string('update')->default('');
            $table->string('status')->default('Pending');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hazard_report');
    }
};
