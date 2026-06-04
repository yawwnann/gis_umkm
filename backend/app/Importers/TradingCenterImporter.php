<?php

namespace App\Importers;

use App\Models\TradingCenter;

class TradingCenterImporter extends BaseGeoJSONImporter
{
    public function __construct()
    {
        parent::__construct('data/niaga.geojson');
    }

    public function import(): int
    {
        $count = 0;

        foreach ($this->features as $feature) {
            $props = $feature['properties'] ?? [];
            $geom = $feature['geometry'] ?? [];

            $name = $props['NAMOBJ'] ?? $props['name'] ?? null;

            if (empty($name)) {
                continue;
            }

            $coords = $this->extractCoordinates($geom);
            $type = $this->determineType($props);

            // Skip banks - they are not trading centers
            if (str_contains(strtolower($name), 'bank')) {
                continue;
            }

            // Check if already exists
            $exists = TradingCenter::where('name', 'ilike', $name)->exists();
            if ($exists) {
                continue;
            }

            TradingCenter::create([
                'name' => $name,
                'type' => $type,
                'latitude' => $coords['lat'],
                'longitude' => $coords['lng'],
                'geom' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $coords['lng'], (float) $coords['lat']],
                ],
            ]);

            $count++;
        }

        return $count;
    }

    private function determineType(array $props): string
    {
        $remark = strtolower($props['REMARK'] ?? '');
        $fungsi = $props['FUNGSI'] ?? 0;

        // Based on Fungsi code
        if ($fungsi == 6) {
            return 'pusat_perdagangan';
        }
        if ($fungsi == 7) {
            return 'pasar';
        }
        if ($fungsi == 4) {
            return 'hotel';
        }

        // Based on remark text
        if (str_contains($remark, 'pasar')) {
            return 'pasar';
        }
        if (str_contains($remark, 'mal') || str_contains($remark, 'toserba')) {
            return 'pusat_perdagangan';
        }
        if (str_contains($remark, 'hotel') || str_contains($remark, 'losmen')) {
            return 'hotel';
        }

        return 'pertokoan';
    }
}
