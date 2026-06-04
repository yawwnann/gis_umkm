<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'latitude',
        'longitude',
        'geom',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'geom' => 'array',
        ];
    }

    public function toGeoJSON(): array
    {
        return [
            'type' => 'Feature',
            'properties' => [
                'id' => $this->id,
                'name' => $this->name,
                'level' => $this->level,
            ],
            'geometry' => $this->geom,
        ];
    }
}