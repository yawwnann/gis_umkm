<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Village extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'population',
        'area_km2',
        'density',
        'geom',
    ];

    protected function casts(): array
    {
        return [
            'population' => 'integer',
            'area_km2' => 'decimal:4',
            'density' => 'decimal:4',
            'geom' => 'array', // Auto-cast from JSONB
        ];
    }

    public function umkms(): HasMany
    {
        return $this->hasMany(Umkm::class);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'ilike', "%{$term}%");
    }
}