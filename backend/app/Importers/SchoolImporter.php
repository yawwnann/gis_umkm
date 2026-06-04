<?php

namespace App\Importers;

use App\Models\School;

class SchoolImporter extends BaseGeoJSONImporter
{
    public function __construct()
    {
        parent::__construct('data/Sekolah.geojson');
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
            $level = $this->determineLevel($props);

            // Check if already exists
            $exists = School::where('name', 'ilike', $name)->exists();
            if ($exists) {
                continue;
            }

            School::create([
                'name' => $name,
                'level' => $level,
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

    private function determineLevel(array $props): string
    {
        $remark = strtolower($props['REMARK'] ?? '');
        $fggpdk = $props['FGGPDK'] ?? 0;

        // Based on FGGPDK code
        $levelMap = [
            1 => 'pt',
            2 => 'sma',
            3 => 'smp',
            4 => 'sd',
            6 => 'tk',
            10 => 'keagamaan',
            999 => 'khusus',
        ];

        if (isset($levelMap[$fggpdk])) {
            return $levelMap[$fggpdk];
        }

        // Based on remark text
        if (str_contains($remark, 'perguruan tinggi') || str_contains($remark, 'pendidikan tinggi')) {
            return 'pt';
        }
        if (str_contains($remark, 'menengah umum') || str_contains($remark, 'sma')) {
            return 'sma';
        }
        if (str_contains($remark, 'menengah pertama') || str_contains($remark, 'smp')) {
            return 'smp';
        }
        if (str_contains($remark, 'dasar') || str_contains($remark, 'sd')) {
            return 'sd';
        }
        if (str_contains($remark, 'tk') || str_contains($remark, 'anak usia dini') || str_contains($remark, 'paud')) {
            return 'tk';
        }
        if (str_contains($remark, 'keagamaan')) {
            return 'keagamaan';
        }

        return 'lainnya';
    }
}
