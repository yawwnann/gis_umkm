<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UmkmResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'owner' => $this->owner,
            'category' => $this->category,
            'address' => $this->address,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'potential_score' => $this->potential_score ? (float) $this->potential_score : null,
            'potential_level' => $this->potential_level ? strtolower($this->potential_level->name) : null,
            'potential_label' => $this->potential_level?->label(),
            'potential_color' => $this->potential_level?->color(),
            'village' => new VillageResource($this->whenLoaded('village')),
            'village_id' => $this->village_id,
            'village_name' => $this->village?->name,
            'photos' => $this->when($this->relationLoaded('photos'), function () {
                return $this->photos->map(fn($photo) => [
                    'id' => $photo->id,
                    'url' => $photo->url,
                    'is_primary' => $photo->is_primary,
                ]);
            }),
            'primary_photo_url' => $this->primaryPhoto?->url,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}