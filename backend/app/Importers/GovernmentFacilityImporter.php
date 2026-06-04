<?php

namespace App\Importers;

use App\Models\GovernmentFacility;

class GovernmentFacilityImporter extends BaseGeoJSONImporter
{
    public function __construct()
    {
        parent::__construct('data/pemerintahan.geojson');
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

            // Check if already exists
            $exists = GovernmentFacility::where('name', 'ilike', $name)->exists();
            if ($exists) {
                continue;
            }

            GovernmentFacility::create([
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
        $fgsgov = $props['FGSGOV'] ?? 0;

        // Based on FGSGOV code
        $typeMap = [
            8 => 'kantor_bupati',
            9 => 'kantor_kecamatan',
            10 => 'kantor_kelurahan',
            16 => 'kantor_polisi',
            20 => 'pertahanan',
            999 => 'lainnya',
        ];

        if (isset($typeMap[$fgsgov])) {
            return $typeMap[$fgsgov];
        }

        // Based on remark text
        if (str_contains($remark, 'bupati')) {
            return 'kantor_bupati';
        }
        if (str_contains($remark, 'camat')) {
            return 'kantor_kecamatan';
        }
        if (str_contains($remark, 'lurah') || str_contains($remark, 'kelurahan') || str_contains($remark, 'kepala desa')) {
            return 'kantor_kelurahan';
        }
        if (str_contains($remark, 'polisi') || str_contains($remark, 'polsek')) {
            return 'kantor_polisi';
        }
        if (str_contains($remark, ' Brimob') || str_contains($remark, 'pertahanan')) {
            return 'pertahanan';
        }

        return 'lainnya';
    }
}
