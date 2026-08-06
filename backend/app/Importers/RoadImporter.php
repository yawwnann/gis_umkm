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

            // Skip features without geometry
            if (empty($geom)) {
                continue;
            }

            Road::create([
                'osm_id'  => $props['osm_id'] ?? null,
                'name'    => $props['name'] ?? null,
                'highway' => $props['highway'] ?? null,
                'surface' => $props['surface'] ?? null,
                'oneway'  => $props['oneway'] ?? null,
                'geom'    => $this->toGeoJSON($geom),
            ]);

            $count++;
        }

        return $count;
    }
}
