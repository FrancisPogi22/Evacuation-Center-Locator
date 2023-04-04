<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('disaster', function (Blueprint $table) {
            $table->id("disaster_number");
            $table->string("disaster_label")->nullable();
        });
    }
   
    public function down(): void
    {
        Schema::dropIfExists('disaster');
    }
};
