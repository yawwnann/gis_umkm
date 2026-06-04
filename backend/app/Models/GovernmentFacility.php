<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GovernmentFacility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
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
                'type' => $this->type,
            ],
            'geometry' => $this->geom,
        ];
    }
}