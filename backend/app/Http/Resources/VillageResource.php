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
            'umkm_count' => (int) ($this->umkms_count ?? $this->umkm_count ?? 0),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}