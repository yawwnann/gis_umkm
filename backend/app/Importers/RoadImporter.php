<?php

namespace App\Importers;

use App\Models\Road;

class RoadImporter extends BaseGeoJSONImporter
{
    public function __construct()
    {
        parent::__construct('data/jalan_sungailiat.geojson');
    }

    public function import(): int
    {
        $count = 0;

        foreach ($this->features as $feature) {
            $props = $feature['properties'] ?? [];
            $geom = $feature['geometry'] ?? [];

            $name = $props['NAMRJL'] ?? $props['name'] ?? null;
            $highway = $props['REMARK'] ?? $props['highway'] ?? null;
            $surface = $props['KONSTR'] ?? $props['surface'] ?? null;

            // Generate a unique identifier for unnamed roads
            if (!$name || strlen($name) < 3 || str_contains(strtolower($name), 'tanpa nama')) {
                $osmId = $props['osm_id'] ?? null;
                $name = $osmId ? "Jalan (OSM {$osmId})" : 'Jalan Tanpa Nama';
            }

            // Skip if already exists
            $exists = Road::where('name', $name)
                ->where('osm_id', $props['osm_id'] ?? null)
                ->exists();

            if ($exists) {
                continue;
            }

            // Normalize highway type
            $highwayType = $this->normalizeHighway($highway);

            Road::create([
                'osm_id' => $props['osm_id'] ?? null,
                'name' => $name,
                'highway' => $highwayType,
                'surface' => $surface,
                'oneway' => in_array(strtolower($highway ?? ''), ['satu arah', 'oneway']),
                'length_m' => $props['length_m'] ?? $props['SHAPE_Leng'] ?? null,
                'geom' => $this->toGeoJSON($geom),
            ]);

            $count++;
        }

        return $count;
    }

    private function normalizeHighway(?string $highway): string
    {
        if (!$highway) {
            return ' jalan ';
        }

        $highway = strtolower($highway);

        if (str_contains($highway, 'arteri') || str_contains($highway, 'primer')) {
            return 'primary';
        }
        if (str_contains($highway, 'kolektor') || str_contains($highway, 'sekunder')) {
            return 'secondary';
        }
        if (str_contains($highway, 'lokal') || str_contains($highway, 'desa')) {
            return 'tertiary';
        }
        if (str_contains($highway, 'satu arah') || str_contains($highway, 'one way')) {
            return 'oneway';
        }

        return 'residential';
    }
}
