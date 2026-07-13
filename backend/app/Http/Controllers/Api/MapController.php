<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GovernmentFacility;
use App\Models\Road;
use App\Models\Settlement;
use App\Models\Tourism;
use App\Models\TradingCenter;
use App\Models\Umkm;
use App\Models\School;
use App\Models\Village;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function umkms(Request $request): JsonResponse
    {
        $query = Umkm::query();

        // Optional filters
        if ($request->has('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $umkms = $query->get();

        $features = $umkms->map(fn($umkm) => $umkm->toGeoJSON())->toArray();

        return $this->geoJSONResponse('UMKM Kuliner', $features);
    }

    public function villages(): JsonResponse
    {
        $villages = Village::withCount('umkms')
            ->selectRaw('*, 
                (SELECT COUNT(*) FROM schools WHERE ST_Contains(ST_GeomFromGeoJSON(villages.geom::text), ST_GeomFromGeoJSON(schools.geom::text))) as schools_count,
                (SELECT COUNT(*) FROM government_facilities WHERE ST_Contains(ST_GeomFromGeoJSON(villages.geom::text), ST_GeomFromGeoJSON(government_facilities.geom::text))) as gov_count'
            )
            ->get();

        $features = $villages->map(function ($village) {
            $avgPotential = $village->umkms()->avg('potential_score');

            return [
                'type' => 'Feature',
                'properties' => [
                    'id' => $village->id,
                    'name' => $village->name,
                    'population' => $village->population,
                    'schools_count' => $village->schools_count,
                    'gov_count' => $village->gov_count,
                    'area_km2' => $village->area_km2 ? (float) $village->area_km2 : null,
                    'density' => $village->density ? (float) round($village->density, 1) : null,
                    'umkm_count' => $village->umkms_count,
                    'avg_potential_score' => $avgPotential ? (float) round($avgPotential, 1) : null,
                ],
                'geometry' => $village->geom,
            ];
        })->toArray();

        return $this->geoJSONResponse('Batas Wilayah Kelurahan', $features);
    }


    public function settlements(): JsonResponse
    {
        $settlements = Settlement::all();

        $features = $settlements->map(fn($s) => $s->toGeoJSON())->toArray();

        return $this->geoJSONResponse('Kawasan Pemukiman', $features);
    }

    public function tradingCenters(): JsonResponse
    {
        $centers = TradingCenter::all();

        $features = $centers->map(fn($c) => $c->toGeoJSON())->toArray();

        return $this->geoJSONResponse('Pusat Niaga', $features);
    }

    public function schools(): JsonResponse
    {
        $schools = School::all();

        $features = $schools->map(fn($s) => $s->toGeoJSON())->toArray();

        return $this->geoJSONResponse('Fasilitas Pendidikan', $features);
    }

    public function governmentFacilities(): JsonResponse
    {
        $facilities = GovernmentFacility::all();

        $features = $facilities->map(fn($f) => $f->toGeoJSON())->toArray();

        return $this->geoJSONResponse('Fasilitas Pemerintahan', $features);
    }

    public function tourisms(): JsonResponse
    {
        $tourisms = Tourism::all();

        $features = $tourisms->map(fn($t) => $t->toGeoJSON())->toArray();

        return $this->geoJSONResponse('Objek Wisata', $features);
    }

    public function heatmapUmkm(): JsonResponse
    {
        $umkms = Umkm::whereNotNull('latitude')->whereNotNull('longitude')->get();
        
        // Group nearby UMKM by rounding coordinates to ~100m resolution
        // Use count as intensity so dense areas glow brighter
        $grouped = $umkms->groupBy(function ($umkm) {
            return round((float) $umkm->latitude, 3) . ',' . round((float) $umkm->longitude, 3);
        });

        $data = $grouped->map(function ($group) {
            $lat = round((float) $group->first()->latitude, 3);
            $lng = round((float) $group->first()->longitude, 3);
            $count = $group->count();
            // Intensity scales with count (cap at 1.0)
            $intensity = min(1.0, $count / 5);
            return [(float) $lat, (float) $lng, $intensity];
        })->values();

        return response()->json([
            'data' => $data
        ]);
    }

    public function heatmapPotential(): JsonResponse
    {
        $umkms = Umkm::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereNotNull('potential_score')
            ->get();

        // Use fixed potential level thresholds for intensity anchors
        // so heatmap is consistent regardless of actual score distribution
        $data = $umkms->map(function ($umkm) {
            $score = (float) $umkm->potential_score;

            // Fixed thresholds: rendah < 40, sedang 40-69, tinggi >= 70
            // Normalize to 0-1 range using fixed anchors
            $intensity = match (true) {
                $score >= 70 => 0.75 + (min($score, 100) - 70) / 30 * 0.25, // 0.75-1.0
                $score >= 40 => 0.35 + ($score - 40) / 30 * 0.40,           // 0.35-0.75
                default      => max(0, $score / 40 * 0.35),                  // 0.0-0.35
            };

            return [
                (float) $umkm->latitude,
                (float) $umkm->longitude,
                $intensity,
            ];
        });

        return response()->json([
            'data' => $data
        ]);
    }

    private function geoJSONResponse(string $name, array $features): JsonResponse
    {
        return response()->json([
            'type' => 'FeatureCollection',
            'name' => $name,
            'crs' => [
                'type' => 'name',
                'properties' => ['name' => 'urn:ogc:def:crs:EPSG::4326'],
            ],
            'features' => $features,
        ]);
    }
}