<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_report', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('location')->nullable();
            $table->string('photo')->nullable();
            $table->string('status');
            $table->string('user_ip');
            $table->boolean('is_archive');
            $table->string('report_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_report');
    }
};
