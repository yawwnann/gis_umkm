<?php

namespace App\Importers;

use App\Models\Village;
use App\Models\Umkm;
use App\Models\Road;
use App\Models\TradingCenter;
use App\Models\School;
use App\Models\GovernmentFacility;
use App\Models\Tourism;
use Illuminate\Support\Facades\DB;

abstract class BaseGeoJSONImporter
{
    protected string $filePath;
    protected array $features = [];

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function load(): static
    {
        $fullPath = base_path($this->filePath);

        if (!file_exists($fullPath)) {
            throw new \Exception("File not found: {$fullPath}");
        }

        $data = json_decode(file_get_contents($fullPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON: " . json_last_error_msg());
        }

        $this->features = $data['features'] ?? [];

        return $this;
    }

    protected function extractCoordinates(array $geometry): array
    {
        $coords = $geometry['coordinates'] ?? [];

        if ($geometry['type'] === 'Point') {
            return [
                'lng' => $coords[0],
                'lat' => $coords[1],
            ];
        }

        // For Polygon/LineString, get centroid or first point
        if (in_array($geometry['type'], ['Polygon', 'LineString'])) {
            if ($geometry['type'] === 'Polygon') {
                $coords = $coords[0] ?? $coords;
            }

            $lng = array_sum(array_column($coords, 0)) / count($coords);
            $lat = array_sum(array_column($coords, 1)) / count($coords);

            return [
                'lng' => $lng,
                'lat' => $lat,
            ];
        }

        return ['lng' => null, 'lat' => null];
    }

    protected function toGeoJSON(array $geometry): array
    {
        return $geometry;
    }

    abstract public function import(): int;
}
