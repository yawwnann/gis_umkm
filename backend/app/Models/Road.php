<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Road extends Model
{
    use HasFactory;

    protected $fillable = [
        'osm_id',
        'name',
        'highway',
        'surface',
        'oneway',
        'length_m',
        'geom',
    ];

    protected function casts(): array
    {
        return [
            'oneway' => 'boolean',
            'length_m' => 'decimal:2',
            'geom' => 'array',
        ];
    }

    public function toGeoJSON(): array
    {
        return [
            'type' => 'Feature',
            'properties' => [
                'id' => $this->id,
                'osm_id' => $this->osm_id,
                'name' => $this->name,
                'highway' => $this->highway,
                'surface' => $this->surface,
                'oneway' => $this->oneway,
                'length_m' => $this->length_m ? (float) $this->length_m : null,
            ],
            'geometry' => $this->geom,
        ];
    }
}