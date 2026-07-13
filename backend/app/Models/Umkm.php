<?php

namespace App\Models;

use App\Enums\PotentialLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Umkm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner',
        'category',
        'address',
        'latitude',
        'longitude',
        'geom',
        'potential_score',
        'potential_level',
        'village_id',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'potential_score' => 'decimal:2',
            'potential_level' => PotentialLevel::class,
            'geom' => 'array',
        ];
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(UmkmPhoto::class)->orderBy('order');
    }

    public function primaryPhoto(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UmkmPhoto::class)
            ->where('is_primary', true)
            ->oldestOfMany('order');
    }

    public function scopeSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }
        return $query
            ->where('name', 'ilike', "%{$term}%")
            ->orWhere('owner', 'ilike', "%{$term}%");
    }

    public function scopeFilterByCategory($query, $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }
        return $query;
    }

    public function scopeFilterByVillage($query, $villageId)
    {
        if ($villageId) {
            return $query->where('village_id', $villageId);
        }
        return $query;
    }

    public function scopeFilterByPotential($query, $level)
    {
        if ($level) {
            return $query->where('potential_level', $level);
        }
        return $query;
    }

    public function scopeFilterByCategoryIn($query, array $categories)
    {
        if (!empty($categories)) {
            return $query->whereIn('category', $categories);
        }
        return $query;
    }

    public function toGeoJSON(): array
    {
        return [
            'type' => 'Feature',
            'properties' => [
                'id' => $this->id,
                'name' => $this->name,
                'owner' => $this->owner,
                'category' => $this->category,
                'address' => $this->address,
                'potential_score' => $this->potential_score ? (float) $this->potential_score : null,
                'potential_level' => $this->potential_level ? strtolower($this->potential_level->name) : null,
                'village_id' => $this->village_id,
                'village_name' => $this->village?->name,
            ],
            'geometry' => $this->geom,
        ];
    }
}