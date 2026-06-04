<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VillageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'population' => $this->population,
            'area_km2' => $this->area_km2 ? (float) $this->area_km2 : null,
            'density' => $this->density ? (float) $this->density : null,
            'umkm_count' => $this->when(
                isset($this->umkm_count),
                fn() => (int) $this->umkm_count
            ),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}