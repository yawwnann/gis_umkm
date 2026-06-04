<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('government_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // kantor_kecamatan, kantor_kelurahan, kantor_pemerintah
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->jsonb('geom'); // GeoJSON Point
            $table->timestamps();

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('government_facilities');
    }
};