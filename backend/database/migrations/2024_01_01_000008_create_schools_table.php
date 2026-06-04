<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('level'); // sd, smp, sma, pt
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->jsonb('geom'); // GeoJSON Point
            $table->timestamps();

            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};