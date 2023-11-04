<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resident_report', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('details')->nullable();
            $table->string('photo')->nullable();
            $table->string('status')->default('Pending');
            $table->string('user_ip');
            $table->boolean('is_archive')->default(false);
            $table->string('report_time');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('notification')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_report');
    }
};
