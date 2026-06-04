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
            $table->bigInteger('osm_id')->nullable();
            $table->string('name')->nullable();
            $table->string('highway')->nullable(); // residential, secondary, primary, etc.
            $table->string('surface')->nullable(); // asphalt, paved, unpaved
            $table->boolean('oneway')->default(false);
            $table->decimal('length_m', 12, 2)->nullable();
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