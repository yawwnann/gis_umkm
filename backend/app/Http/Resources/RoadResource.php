<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'osm_id' => $this->osm_id,
            'name' => $this->name,
            'highway' => $this->highway,
            'surface' => $this->surface,
            'oneway' => $this->oneway,
            'length_m' => $this->length_m ? (float) $this->length_m : null,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}