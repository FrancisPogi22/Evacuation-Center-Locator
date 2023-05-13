<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evacuation_center', function (Blueprint $table) {
            $table->id('evacuation_center_id');
            $table->string('evacuation_center_name');
            $table->string('evacuation_center_contact');
            $table->string('evacuation_center_address');
            $table->foreignId('barangay_id')->references('barangay_id')->on('barangay')->cascadeOnDelete()->cascadeOnUpdate()->nullable();
            $table->string('latitude');
            $table->string('longitude');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evacuation_center');
    }
};