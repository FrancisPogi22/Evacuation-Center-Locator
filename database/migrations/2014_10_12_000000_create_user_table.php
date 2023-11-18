<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('organization');
            $table->string('position');
            $table->string('status');
            $table->boolean('is_disable');
            $table->boolean('is_archive');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
