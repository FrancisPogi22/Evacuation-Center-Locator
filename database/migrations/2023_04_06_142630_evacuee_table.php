<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evacuee', function (Blueprint $table) {
            $table->id();
            $table->integer('infants');
            $table->integer('minors');
            $table->integer('senior_citizen');
            $table->integer('pwd');
            $table->integer('pregnant');
            $table->integer('lactating');
            $table->integer('families');
            $table->integer('individuals');
            $table->integer('male');
            $table->integer('female');
            $table->foreignId('disaster_id')->references('id')->on('disaster');
            $table->string('date_entry');
            $table->string('barangay');
            $table->string('evacuation_assigned');
            $table->string('remarks')->nullable();
            $table->boolean('is_archive');
            $table->foreignId('user_id')->references('id')->on('user')->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evacuee');
    }
};
