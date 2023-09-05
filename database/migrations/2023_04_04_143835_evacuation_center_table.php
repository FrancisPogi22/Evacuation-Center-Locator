<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evacuation_center', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('barangay_name');
            $table->string('latitude');
            $table->string('longitude');
            $table->integer('capacity');
            $table->string('status');
            $table->boolean('is_archive');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evacuation_center');
    }
};
