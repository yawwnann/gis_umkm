<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'geom',
    ];

    protected function casts(): array
    {
        return [
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
            ],
            'geometry' => $this->geom,
        ];
    }
}