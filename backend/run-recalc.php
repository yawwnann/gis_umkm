<?php
// Run: php run-recalc.php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\PotentialAnalysisService;

echo "=== Recalculating potentials ===\n";
$svc = new PotentialAnalysisService();
$count = 0;
\App\Models\Umkm::chunk(100, function($umkms) use ($svc, &$count) {
    foreach ($umkms as $u) {
        $r = $svc->calculateForUmkm($u);
        $u->updateQuietly(['potential_score' => $r['score'], 'potential_level' => $r['level']]);
        $count++;
    }
});
echo "Updated: {$count} UMKM\n\n";

echo "=== Distribution ===\n";
$stats = \App\Models\Umkm::selectRaw('potential_level, count(*) as cnt, avg(potential_score) as avg')
    ->whereNotNull('potential_level')
    ->groupBy('potential_level')
    ->get();
foreach ($stats as $s) {
    echo "  Level " . $s->potential_level->value . ": {$s->cnt} UMKM, avg score: " . round($s->avg, 1) . "\n";
}

echo "\n=== Sample Breakdown (3 UMKMs) ===\n";
$svc2 = new PotentialAnalysisService();
\App\Models\Umkm::with('village')->limit(3)->get()->each(function($u) use ($svc2) {
    $r = $svc2->calculateForUmkm($u);
    echo "UMKM: {$u->name} | Village: " . ($u->village?->name ?? 'N/A') . " (density: " . round($u->village?->density ?? 0, 1) . "/km²)\n";
    echo "  Scores: Trading={$r['breakdown']['trading_score']} Settlement={$r['breakdown']['settlement_score']} Density={$r['breakdown']['density_score']}\n";
    echo "  TOTAL: {$r['score']} → {$r['level']->name}\n\n";
});

echo "Done.\n";