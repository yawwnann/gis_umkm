<?php

namespace App\Importers;

use App\Models\Village;

class VillageImporter extends BaseGeoJSONImporter
{
    public function __construct()
    {
        parent::__construct('data/batas_wilayah.geojson');
    }

    public function import(): int
    {
        $count = 0;

        foreach ($this->features as $feature) {
            $props = $feature['properties'] ?? [];
            $geom = $feature['geometry'] ?? [];

            // Extract village name from various possible field names
            $name = $props['NAMOBJ'] ?? $props['name'] ?? $props['NAMAKEL'] ?? 'Unknown';

            // Skip if name is empty or not a village name
            if (empty($name) || strlen($name) < 3) {
                continue;
            }

            // Check if village already exists
            $exists = Village::where('name', 'ilike', $name)->exists();
            if ($exists) {
                continue;
            }

            Village::create([
                'name' => $name,
                'population' => $props['JML_PDDK'] ?? $props['population'] ?? null,
                'area_km2' => $props['LUAS'] ?? $props['area_km2'] ?? null,
                'density' => null,
                'geom' => $this->toGeoJSON($geom),
            ]);

            $count++;
        }

        return $count;
    }
}
