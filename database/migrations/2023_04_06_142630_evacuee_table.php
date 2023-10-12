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
            $table->integer('individuals');
            $table->integer('male');
            $table->integer('female');
            $table->string('barangay');
            $table->string('family_head');
            $table->string('birth_date');
            $table->foreignId('family_id')->references('id')->on('family_record')->cascadeOnUpdate();
            $table->foreignId('disaster_id')->references('id')->on('disaster')->cascadeOnUpdate();
            $table->foreignId('evacuation_id')->references('id')->on('evacuation_center')->cascadeOnUpdate();
            $table->foreignId('user_id')->references('id')->on('user')->cascadeOnUpdate();
            $table->string('status')->default('Evacuated');
            $table->boolean('is_archive')->default(0);
            $table->timestamp('updated_at')->default(date('Y-m-d H:i:s'));
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evacuee');
    }
};
