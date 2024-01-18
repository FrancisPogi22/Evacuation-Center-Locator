<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('disaster', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('year');
            $table->string('status')->default('On Going');
            $table->boolean('is_archive')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disaster');
    }
};
