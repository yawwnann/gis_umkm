<?php

namespace App\Services;

use App\Models\Umkm;
use App\Models\Village;
use App\Enums\PotentialLevel;
use Illuminate\Support\Facades\DB;

class PotentialAnalysisService
{
    // Max distances in meters for normalization
    private const MAX_ROAD_DISTANCE = 1000;       // 1km — proximity ke jalan utama
    private const MAX_TRADING_DISTANCE = 3000;     // 3km — proximity ke pusat niaga
    private const MAX_SETTLEMENT_DISTANCE = 2000;  // 2km — proximity ke pemukiman

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
            // Fallbacks sesuai PRD — total harus = 1.0
            self::$weights = [
                'road'               => $weights['road'] ?? 0.40,
                'trading'            => $weights['trading'] ?? 0.30,
                'settlement'         => $weights['settlement'] ?? 0.20,
                'population_density' => $weights['population_density'] ?? 0.10,
            ];
        }
        return self::$weights;
    }

    /**
     * Calculate potential score for a single UMKM using Weighted Overlay
     * sesuai PRD:
     *   Akses Jalan          → 40%
     *   Kedekatan Fasilitas Niaga → 30%
     *   Kawasan Pemukiman    → 20%
     *   Kepadatan Penduduk   → 10%
     */
    public function calculateForUmkm(Umkm $umkm): array
    {
        $weights = $this->getWeights();

        // 1. Road proximity score (40%)
        $roadScore = $this->calculateRoadScore($umkm->geom);

        // 2. Trading center proximity score (30%)
        $tradingScore = $this->calculateTradingScore($umkm->geom);

        // 3. Settlement proximity score (20%)
        $settlementScore = $this->calculateSettlementScore($umkm->geom);

        // 4. Population density score (10%)
        $densityScore = $this->calculateDensityScore($umkm->village);

        // Calculate total score (0-100)
        $totalScore = (
            $weights['road'] * $roadScore +
            $weights['trading'] * $tradingScore +
            $weights['settlement'] * $settlementScore +
            $weights['population_density'] * $densityScore
        );

        $level = $this->determineLevel($totalScore);

        return [
            'score' => round($totalScore, 2),
            'level' => $level,
            'breakdown' => [
                'road_score'       => round($roadScore, 2),
                'trading_score'    => round($tradingScore, 2),
                'settlement_score' => round($settlementScore, 2),
                'density_score'    => round($densityScore, 2),
            ],
        ];
    }

    /**
     * Recalculate all UMKM potentials
     */
    public function recalculateAll(): int
    {
        // Reset caches
        self::$densityMaxThreshold = null;
        self::$weights = null;

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
     * Semakin dekat dengan jalan maka skor semakin tinggi.
     * Score = 100 when distance = 0, decreasing linearly to 0 at MAX_ROAD_DISTANCE
     */
    private function calculateRoadScore(array $geom): float
    {
        $lon = $geom['coordinates'][0];
        $lat = $geom['coordinates'][1];

        $minDistance = DB::selectOne(
            "SELECT ST_Distance(
                ST_Transform(ST_SetSRID(ST_MakePoint(?::float8, ?::float8), 4326), 3857),
                ST_Transform(ST_GeomFromGeoJSON(roads.geom::text), 3857)
            ) as distance
            FROM roads
            WHERE roads.geom IS NOT NULL
            ORDER BY distance
            LIMIT 1",
            [$lon, $lat]
        )?->distance ?? self::MAX_ROAD_DISTANCE;

        return max(0, 100 - ($minDistance / self::MAX_ROAD_DISTANCE * 100));
    }

    /**
     * Calculate trading center proximity score
     * Semakin dekat dengan pasar/pusat perdagangan maka skor semakin tinggi.
     * Score = 100 when distance = 0, decreasing linearly to 0 at MAX_TRADING_DISTANCE
     */
    private function calculateTradingScore(array $geom): float
    {
        $lon = $geom['coordinates'][0];
        $lat = $geom['coordinates'][1];

        $minDistance = DB::selectOne(
            "SELECT ST_Distance(
                ST_Transform(ST_SetSRID(ST_MakePoint(?::float8, ?::float8), 4326), 3857),
                ST_Transform(ST_GeomFromGeoJSON(trading_centers.geom::text), 3857)
            ) as distance
            FROM trading_centers
            WHERE trading_centers.geom IS NOT NULL
            ORDER BY distance
            LIMIT 1",
            [$lon, $lat]
        )?->distance ?? self::MAX_TRADING_DISTANCE;

        return max(0, 100 - ($minDistance / self::MAX_TRADING_DISTANCE * 100));
    }

    /**
     * Calculate settlement proximity score
     * Semakin dekat dengan kawasan pemukiman maka skor semakin tinggi.
     * Score = 100 when distance = 0, decreasing linearly to 0 at MAX_SETTLEMENT_DISTANCE
     */
    private function calculateSettlementScore(array $geom): float
    {
        $lon = $geom['coordinates'][0];
        $lat = $geom['coordinates'][1];

        $minDistance = DB::selectOne(
            "SELECT ST_Distance(
                ST_Transform(ST_SetSRID(ST_MakePoint(?::float8, ?::float8), 4326), 3857),
                ST_Transform(ST_GeomFromGeoJSON(settlements.geom::text), 3857)
            ) as distance
            FROM settlements
            WHERE settlements.geom IS NOT NULL
            ORDER BY distance
            LIMIT 1",
            [$lon, $lat]
        )?->distance ?? self::MAX_SETTLEMENT_DISTANCE;

        return max(0, 100 - ($minDistance / self::MAX_SETTLEMENT_DISTANCE * 100));
    }

    /**
     * Calculate density score based on village population density
     *
     * Uses a data-driven max threshold (second highest density)
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
     * Thresholds berdasarkan 4-factor weighted model (total bobot = 1.0):
     *   >= 70: Tinggi  (mendapat manfaat maksimal dari infrastruktur & demografi)
     *   40-69: Sedang  (terdampak tapi tidak optimal)
     *   < 40:  Rendah  (terisolasi atau di area jarang penduduk)
     */
    private function determineLevel(float $score): PotentialLevel
    {
        if ($score >= 70) {
            return PotentialLevel::Tinggi;
        } elseif ($score >= 40) {
            return PotentialLevel::Sedang;
        } else {
            return PotentialLevel::Rendah;
        }
    }
}
