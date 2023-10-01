<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_log', function (Blueprint $table) {
            $table->id();
            $table->string('user_ip');
            $table->tinyInteger('attempt');
            $table->timestamp('report_time')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_log');
    }
};
