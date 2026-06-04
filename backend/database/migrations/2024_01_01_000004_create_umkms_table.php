<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('owner');
            $table->string('category');
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->jsonb('geom'); // GeoJSON Point
            $table->decimal('potential_score', 5, 2)->nullable();
            $table->unsignedTinyInteger('potential_level')->nullable(); // 1=Tinggi, 2=Sedang, 3=Rendah
            $table->foreignId('village_id')->nullable()->constrained('villages')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};