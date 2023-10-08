<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_record', function (Blueprint $table) {
            $table->id();
            $table->integer('infants');
            $table->integer('minors');
            $table->integer('senior_citizen');
            $table->integer('pwd');
            $table->integer('pregnant');
            $table->integer('lactating');
            $table->integer('male');
            $table->integer('female');
            $table->integer('individuals');
            $table->string('barangay');
            $table->string('family_head');
            $table->string('birth_date');
            $table->foreignId('user_id')->references('id')->on('user')->cascadeOnUpdate();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_record');
    }
};
