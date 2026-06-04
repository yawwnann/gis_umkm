<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TradingCenterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}