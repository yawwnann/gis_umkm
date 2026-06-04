<?php

namespace App\Importers;

use App\Models\Tourism;

class TourismImporter extends BaseGeoJSONImporter
{
    public function __construct()
    {
        parent::__construct('data/wisata.geojson');
    }

    public function import(): int
    {
        $count = 0;

        foreach ($this->features as $feature) {
            $props = $feature['properties'] ?? [];
            $geom = $feature['geometry'] ?? [];

            $name = $props['name'] ?? $props['NAMOBJ'] ?? null;

            if (empty($name)) {
                continue;
            }

            $coords = $this->extractCoordinates($geom);
            $type = $this->determineType($props);
            $description = $this->buildDescription($props);

            // Check if already exists
            $exists = Tourism::where('name', 'ilike', $name)->exists();
            if ($exists) {
                continue;
            }

            Tourism::create([
                'name' => $name,
                'type' => $type,
                'description' => $description,
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
        $tourism = strtolower($props['tourism'] ?? '');
        $remark = strtolower($props['remark'] ?? '');
        $name = strtolower($props['name'] ?? '');

        if (str_contains($tourism, 'hotel') || str_contains($tourism, 'hostel') || str_contains($remark, 'hotel')) {
            return 'hotel';
        }
        if (str_contains($tourism, 'beach') || str_contains($remark, 'pantai') || str_contains($name, 'pantai')) {
            return 'pantai';
        }
        if (str_contains($tourism, 'attraction') || str_contains($tourism, 'artwork') || str_contains($tourism, 'viewpoint')) {
            return 'destinasi_wisata';
        }
        if (str_contains($remark, 'museum') || str_contains($name, 'museum')) {
            return 'museum';
        }

        return 'destinasi_wisata';
    }

    private function buildDescription(array $props): ?string
    {
        $parts = [];

        if (!empty($props['tourism'])) {
            $parts[] = 'Tipe: ' . ucfirst($props['tourism']);
        }

        if (!empty($props['remark'])) {
            $parts[] = $props['remark'];
        }

        if (!empty($props['website'])) {
            $parts[] = 'Website: ' . $props['website'];
        }

        if (!empty($props['phone'])) {
            $parts[] = 'Telepon: ' . $props['phone'];
        }

        return empty($parts) ? null : implode(' | ', $parts);
    }
}
