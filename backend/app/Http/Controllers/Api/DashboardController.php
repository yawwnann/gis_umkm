<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $totalUmkm = Umkm::count();

        $byPotential = DB::table('umkms')
            ->select('potential_level', DB::raw('count(*) as count'))
            ->whereNotNull('potential_level')
            ->groupBy('potential_level')
            ->pluck('count', 'potential_level');

        $categories = Umkm::distinct()->count('category');
        $villages = \App\Models\Village::count();

        return response()->json([
            'data' => [
                'total_umkm' => $totalUmkm,
                'total_categories' => $categories,
                'total_villages' => $villages,
                'by_potential' => [
                    'tinggi' => $byPotential[1] ?? 0,
                    'sedang' => $byPotential[2] ?? 0,
                    'rendah' => $byPotential[3] ?? 0,
                ],
            ],
        ]);
    }

    public function byVillage(): JsonResponse
    {
        $data = \App\Models\Village::withCount('umkms')
            ->selectRaw('*, 
                (SELECT COUNT(*) FROM schools WHERE ST_Contains(ST_GeomFromGeoJSON(villages.geom::text), ST_GeomFromGeoJSON(schools.geom::text))) as schools_count,
                (SELECT COUNT(*) FROM government_facilities WHERE ST_Contains(ST_GeomFromGeoJSON(villages.geom::text), ST_GeomFromGeoJSON(government_facilities.geom::text))) as gov_count'
            )
            ->orderBy('umkms_count', 'desc')
            ->get()
            ->map(fn($v) => [
                'id' => $v->id,
                'name' => $v->name,
                'umkm_count' => $v->umkms_count,
                'population' => $v->population,
                'schools_count' => $v->schools_count,
                'gov_count' => $v->gov_count,
                'density' => $v->density ? round((float) $v->density, 1) : null,
            ]);

        return response()->json(['data' => $data]);
    }

    public function byCategory(): JsonResponse
    {
        $data = Umkm::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderByDesc('count')
            ->get()
            ->map(fn($r) => [
                'category' => $r->category,
                'count' => (int) $r->count,
            ]);

        return response()->json(['data' => $data]);
    }

    public function byPotential(): JsonResponse
    {
        $data = DB::table('umkms')
            ->select('potential_level', DB::raw('count(*) as count'))
            ->whereNotNull('potential_level')
            ->groupBy('potential_level')
            ->orderBy('potential_level')
            ->get()
            ->map(fn($r) => [
                'level' => match ((int) $r->potential_level) {
                    1 => 'tinggi',
                    2 => 'sedang',
                    3 => 'rendah',
                    default => (string) $r->potential_level,
                },
                'count' => (int) $r->count,
            ]);

        return response()->json(['data' => $data]);
    }

    public function registrations(): JsonResponse
    {
        $data = DB::table('umkms')
            ->select(
                DB::raw('extract(month from created_at) as month'),
                DB::raw('count(*) as count')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($r) => [
                'month' => (int) $r->month,
                'count' => (int) $r->count,
            ]);

        return response()->json(['data' => $data]);
    }

    public function analysis(): JsonResponse
    {
        $totalUmkm = Umkm::count();

        // Village analysis
        $villageData = \App\Models\Village::withCount('umkms')
            ->orderBy('umkms_count', 'desc')
            ->get()
            ->map(fn($v) => [
                'id' => $v->id,
                'name' => $v->name,
                'umkm_count' => $v->umkms_count,
                'population' => $v->population,
                'density' => $v->density ? round((float) $v->density, 1) : null,
            ]);

        // Category analysis
        $categoryData = Umkm::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderByDesc('count')
            ->get()
            ->map(fn($r) => [
                'category' => $r->category,
                'count' => (int) $r->count,
            ]);

        // Potential distribution
        $potentialData = DB::table('umkms')
            ->select('potential_level', DB::raw('count(*) as count'))
            ->whereNotNull('potential_level')
            ->groupBy('potential_level')
            ->orderBy('potential_level')
            ->get()
            ->map(fn($r) => [
                'level' => match ((int) $r->potential_level) {
                    1 => 'tinggi',
                    2 => 'sedang',
                    3 => 'rendah',
                    default => (string) $r->potential_level,
                },
                'count' => (int) $r->count,
            ]);

        $totalPotential = collect($potentialData)->sum('count');

        // Score distribution buckets — corrected boundaries
        $scoreBuckets = [
            ['range' => '0-20', 'min' => 0, 'max' => 20, 'count' => 0],
            ['range' => '21-40', 'min' => 21, 'max' => 40, 'count' => 0],
            ['range' => '41-60', 'min' => 41, 'max' => 60, 'count' => 0],
            ['range' => '61-80', 'min' => 61, 'max' => 80, 'count' => 0],
            ['range' => '81-100', 'min' => 81, 'max' => 100, 'count' => 0],
        ];

        $scores = DB::table('umkms')
            ->whereNotNull('potential_score')
            ->pluck('potential_score');

        $avgScore = $scores->avg();
        $scoreCount = $scores->count();

        foreach ($scores as $score) {
            $matched = false;
            foreach ($scoreBuckets as &$bucket) {
                // Untuk bucket terakhir (81-100), tangkap semua skor >= 81 (termasuk jika ada > 100)
                if ($bucket['range'] === '81-100') {
                    if ($score >= $bucket['min']) {
                        $bucket['count']++;
                        $matched = true;
                        break;
                    }
                } else {
                    if ($score >= $bucket['min'] && $score <= $bucket['max']) {
                        $bucket['count']++;
                        $matched = true;
                        break;
                    }
                }
            }
            // Jika karena alasan presisi desimal belum masuk bucket manapun, masukkan ke bucket terdekat/terakhir
            if (!$matched && count($scoreBuckets) > 0) {
                $scoreBuckets[count($scoreBuckets) - 1]['count']++;
            }
        }
        unset($bucket);

        // Top UMKM by potential score
        $topUmkm = Umkm::whereNotNull('potential_score')
            ->whereNotNull('potential_level')
            ->with('village:id,name')
            ->orderBy('potential_score', 'desc')
            ->take(5)
            ->get()
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'owner' => $u->owner,
                'category' => $u->category,
                'potential_score' => (float) $u->potential_score,
                'potential_level' => $u->potential_level ? strtolower($u->potential_level->name) : null,
                'village_name' => $u->village?->name,
            ]);

        // Category vs Potential cross-analysis
        $categoryPotential = DB::table('umkms')
            ->select(
                'category',
                DB::raw('count(*) as total'),
                DB::raw('avg(potential_score) as avg_score'),
                DB::raw("sum(case when potential_level = 1 then 1 else 0 end) as tinggi"),
                DB::raw("sum(case when potential_level = 2 then 1 else 0 end) as sedang"),
                DB::raw("sum(case when potential_level = 3 then 1 else 0 end) as rendah")
            )
            ->whereNotNull('potential_level')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(fn($r) => [
                'category' => $r->category,
                'total' => (int) $r->total,
                'avg_score' => $r->avg_score ? round((float) $r->avg_score, 1) : 0,
                'tinggi' => (int) $r->tinggi,
                'sedang' => (int) $r->sedang,
                'rendah' => (int) $r->rendah,
            ]);

        return response()->json([
            'data' => [
                'summary' => [
                    'total_umkm' => $totalUmkm,
                    'total_categories' => $categoryData->count(),
                    'total_villages' => $villageData->count(),
                    'avg_potential_score' => $avgScore ? round((float) $avgScore, 1) : 0,
                    'scored_umkm' => $scoreCount,
                ],
                'village_analysis' => $villageData,
                'category_analysis' => $categoryData,
                'potential_distribution' => $potentialData,
                'score_distribution' => $scoreBuckets,
                'top_umkm' => $topUmkm,
                'category_potential' => $categoryPotential,
            ],
        ]);
    }
}
