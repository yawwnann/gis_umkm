<?php

namespace App\Importers;

use App\Models\Settlement;

class SettlementImporter extends BaseGeoJSONImporter
{
    public function __construct()
    {
        parent::__construct('data/pemukiman.geojson');
    }

    public function import(): int
    {
        $count = 0;

        foreach ($this->features as $feature) {
            $props = $feature['properties'] ?? [];
            $geom = $feature['geometry'] ?? [];

            // Extract settlement name from various possible field names
            $name = $props['NAMOBJ'] ?? $props['name'] ?? 'Kawasan Pemukiman ' . ($count + 1);

            // Extract coordinates for latitude/longitude
            $coords = $this->extractCoordinates($geom);

            // Check if similar name already exists
            $exists = Settlement::where('name', 'ilike', '%' . $name . '%')->exists();
            if ($exists) {
                continue;
            }

            Settlement::create([
                'name' => $name,
                'geom' => $this->toGeoJSON($geom),
            ]);

            $count++;
        }

        return $count;
    }
}
