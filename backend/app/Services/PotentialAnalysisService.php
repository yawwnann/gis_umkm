<?php

namespace App\Services;

use App\Models\Umkm;
use App\Models\Road;
use App\Models\TradingCenter;
use App\Models\Settlement;
use App\Models\Village;
use App\Enums\PotentialLevel;
use Illuminate\Support\Facades\DB;

class PotentialAnalysisService
{
    // Max distances in meters for normalization
    private const MAX_ROAD_DISTANCE = 1000;   // 1km — proximity ke jalan utama
    private const MAX_TRADING_DISTANCE = 3000; // 3km — proximity ke pusat niaga
    private const MAX_SETTLEMENT_DISTANCE = 2000; // 2km — proximity ke pemukiman
    private const MAX_SCHOOL_DISTANCE = 2000; // 2km — proximity ke sekolah
    private const MAX_GOV_DISTANCE = 2000; // 2km — proximity ke fasilitas pemerintah

    // Cache for data-driven density threshold (computed once per request)
    private static ?float $densityMaxThreshold = null;
    
    // Cache for AHP weights
    private static ?array $weights = null;

    private function getWeights(): array
    {
        if (self::$weights === null) {
            $records = \App\Models\AnalysisWeight::all();
            $weights = [];
            foreach ($records as $record) {
                $weights[$record->criteria] = $record->weight;
            }
            // Fallbacks in case database is empty
            self::$weights = [
                'road' => $weights['road'] ?? 0.30,
                'trading' => $weights['trading'] ?? 0.25,
                'settlement' => $weights['settlement'] ?? 0.15,
                'education' => $weights['education'] ?? 0.10,
                'government' => $weights['government'] ?? 0.10,
                'population_density' => $weights['population_density'] ?? 0.10,
            ];
        }
        return self::$weights;
    }

    /**
     * Calculate potential score for a single UMKM using AHP Weighted Overlay
     */
    public function calculateForUmkm(Umkm $umkm): array
    {
        $weights = $this->getWeights();

        // Road access score
        $roadScore = $this->calculateRoadScore($umkm->geom);

        // Trading center proximity score
        $tradingScore = $this->calculateTradingScore($umkm->geom);

        // Settlement proximity score
        $settlementScore = $this->calculateSettlementScore($umkm->geom);

        // Population density score
        $densityScore = $this->calculateDensityScore($umkm->village);

        // School proximity score
        $schoolScore = $this->calculateSchoolScore($umkm->geom);

        // Government facility proximity score
        $govScore = $this->calculateGovScore($umkm->geom);

        // Calculate total score using dynamic AHP weights (0-100)
        $totalScore = (
            $weights['road'] * $roadScore +
            $weights['trading'] * $tradingScore +
            $weights['settlement'] * $settlementScore +
            $weights['education'] * $schoolScore +
            $weights['government'] * $govScore +
            $weights['population_density'] * $densityScore
        );

        $level = $this->determineLevel($totalScore);

        return [
            'score' => round($totalScore, 2),
            'level' => $level,
            'breakdown' => [
                'road_score' => round($roadScore, 2),
                'trading_score' => round($tradingScore, 2),
                'settlement_score' => round($settlementScore, 2),
                'school_score' => round($schoolScore, 2),
                'gov_score' => round($govScore, 2),
                'density_score' => round($densityScore, 2),
            ],
        ];
    }

    /**
     * Recalculate all UMKM potentials
     */
    public function recalculateAll(): int
    {
        // Reset density threshold cache
        self::$densityMaxThreshold = null;

        $count = 0;

        Umkm::chunk(100, function ($umkms) use (&$count) {
            foreach ($umkms as $umkm) {
                $result = $this->calculateForUmkm($umkm);

                $umkm->update([
                    'potential_score' => $result['score'],
                    'potential_level' => $result['level'],
                ]);

                $count++;
            }
        });

        return $count;
    }

    /**
     * Calculate road proximity score
     * Score = 100 when distance = 0, decreasing linearly to 0 at MAX_ROAD_DISTANCE
     */
    private function calculateRoadScore(array $geom): float
    {
        $geoJson = json_encode($geom);

        $minDistance = DB::selectOne(
            "SELECT ST_Distance(
                ST_Transform(ST_SetSRID(ST_GeomFromGeoJSON(?), 4326), 3857),
                ST_Transform(ST_GeomFromGeoJSON(roads.geom::text), 3857)
            ) as distance
            FROM roads
            WHERE roads.geom IS NOT NULL
            ORDER BY distance
            LIMIT 1",
            [$geoJson]
        )?->distance ?? self::MAX_ROAD_DISTANCE;

        return max(0, 100 - ($minDistance / self::MAX_ROAD_DISTANCE * 100));
    }

    /**
     * Calculate trading center proximity score
     * Score = 100 when distance = 0, decreasing linearly to 0 at MAX_TRADING_DISTANCE
     */
    private function calculateTradingScore(array $geom): float
    {
        $geoJson = json_encode($geom);

        $minDistance = DB::selectOne(
            "SELECT ST_Distance(
                ST_Transform(ST_SetSRID(ST_GeomFromGeoJSON(?), 4326), 3857),
                ST_Transform(ST_GeomFromGeoJSON(trading_centers.geom::text), 3857)
            ) as distance
            FROM trading_centers
            WHERE trading_centers.geom IS NOT NULL
            ORDER BY distance
            LIMIT 1",
            [$geoJson]
        )?->distance ?? self::MAX_TRADING_DISTANCE;

        return max(0, 100 - ($minDistance / self::MAX_TRADING_DISTANCE * 100));
    }

    /**
     * Calculate settlement proximity score
     * Score = 100 when distance = 0, decreasing linearly to 0 at MAX_SETTLEMENT_DISTANCE
     */
    private function calculateSettlementScore(array $geom): float
    {
        $geoJson = json_encode($geom);

        $minDistance = DB::selectOne(
            "SELECT ST_Distance(
                ST_Transform(ST_SetSRID(ST_GeomFromGeoJSON(?), 4326), 3857),
                ST_Transform(ST_GeomFromGeoJSON(settlements.geom::text), 3857)
            ) as distance
            FROM settlements
            WHERE settlements.geom IS NOT NULL
            ORDER BY distance
            LIMIT 1",
            [$geoJson]
        )?->distance ?? self::MAX_SETTLEMENT_DISTANCE;

        return max(0, 100 - ($minDistance / self::MAX_SETTLEMENT_DISTANCE * 100));
    }

    /**
     * Calculate school proximity score
     * Score = 100 when distance = 0, decreasing linearly to 0 at MAX_SCHOOL_DISTANCE
     */
    private function calculateSchoolScore(array $geom): float
    {
        $geoJson = json_encode($geom);

        $minDistance = DB::selectOne(
            "SELECT ST_Distance(
                ST_Transform(ST_SetSRID(ST_GeomFromGeoJSON(?), 4326), 3857),
                ST_Transform(ST_GeomFromGeoJSON(schools.geom::text), 3857)
            ) as distance
            FROM schools
            WHERE schools.geom IS NOT NULL
            ORDER BY distance
            LIMIT 1",
            [$geoJson]
        )?->distance ?? self::MAX_SCHOOL_DISTANCE;

        return max(0, 100 - ($minDistance / self::MAX_SCHOOL_DISTANCE * 100));
    }

    /**
     * Calculate government facility proximity score
     * Score = 100 when distance = 0, decreasing linearly to 0 at MAX_GOV_DISTANCE
     */
    private function calculateGovScore(array $geom): float
    {
        $geoJson = json_encode($geom);

        $minDistance = DB::selectOne(
            "SELECT ST_Distance(
                ST_Transform(ST_SetSRID(ST_GeomFromGeoJSON(?), 4326), 3857),
                ST_Transform(ST_GeomFromGeoJSON(government_facilities.geom::text), 3857)
            ) as distance
            FROM government_facilities
            WHERE government_facilities.geom IS NOT NULL
            ORDER BY distance
            LIMIT 1",
            [$geoJson]
        )?->distance ?? self::MAX_GOV_DISTANCE;

        return max(0, 100 - ($minDistance / self::MAX_GOV_DISTANCE * 100));
    }

    /**
     * Calculate density score based on village population density
     *
     * Uses a data-driven max threshold (90th percentile of actual densities)
     * instead of an arbitrary fixed value. Falls back to 50 (medium score)
     * if no density data is available.
     */
    private function calculateDensityScore(?Village $village): float
    {
        if (!$village || $village->density === null) {
            return 50; // Default medium score — gunakan jika data density belum ada
        }

        // Compute dynamic threshold once per batch (cached in static var)
        if (self::$densityMaxThreshold === null) {
            $maxDensity = Village::whereNotNull('density')
                ->where('density', '>', 0)
                ->orderByDesc('density')
                ->skip(1) // skip the absolute max to reduce outlier impact
                ->first()?->density;

            // Fallback: use max if skip returns null, minimum 1000
            self::$densityMaxThreshold = $maxDensity ?? max($village->density, 1000);
        }

        // Normalize: score = (density / max_threshold) × 100, capped at 100
        return min(100, ($village->density / self::$densityMaxThreshold) * 100);
    }

    /**
     * Determine potential level from score
     * Thresholds are based on the 4-factor weighted model:
     *   >= 70: Tinggi  (mendapat manfaat maksimal dari infrastruktur & demografi)
     *   40-69: Sedang (terdampak tapi tidak optimal)
     *   < 40:  Rendah  (terisolasi atau di area jarang penduduk)
     */
    private function determineLevel(float $score): PotentialLevel
    {
        if ($score >= 80) {
            return PotentialLevel::Tinggi;
        } elseif ($score >= 60) {
            return PotentialLevel::Sedang;
        } else {
            return PotentialLevel::Rendah;
        }
    }
}
