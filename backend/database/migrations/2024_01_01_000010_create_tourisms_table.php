<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tourisms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // pantai, destinasi_wisata
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('description')->nullable();
            $table->jsonb('geom'); // GeoJSON Point
            $table->timestamps();

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tourisms');
    }
};