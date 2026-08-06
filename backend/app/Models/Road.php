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
        'geom',
    ];

    protected function casts(): array
    {
        return [
            'geom' => 'array',
        ];
    }
}
