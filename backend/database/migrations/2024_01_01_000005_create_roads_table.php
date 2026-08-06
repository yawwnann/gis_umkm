<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roads', function (Blueprint $table) {
            $table->id();
            $table->string('osm_id')->nullable();
            $table->string('name')->nullable();
            $table->string('highway')->nullable(); // residential, tertiary, primary, etc.
            $table->string('surface')->nullable(); // asphalt, gravel, etc.
            $table->string('oneway')->nullable();
            $table->jsonb('geom'); // GeoJSON LineString
            $table->timestamps();

            $table->index('highway');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roads');
    }
};
