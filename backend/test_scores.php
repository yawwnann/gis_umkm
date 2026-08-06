<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = new App\Services\PotentialAnalysisService();

$v = App\Models\Village::find(1); // Bukit Betung
echo 'Village ID: ' . $v->id . PHP_EOL;
$umkms = App\Models\Umkm::where('village_id', $v->id)->get();

$totalVillageScore = 0;
foreach($umkms as $u) {
    $result = $service->calculateForUmkm($u);
    echo 'UMKM: ' . $u->name . ' Score: ' . $result['score'] . PHP_EOL;
    echo '  Road: ' . $result['breakdown']['road_score'] . PHP_EOL;
    echo '  Trading: ' . $result['breakdown']['trading_score'] . PHP_EOL;
    echo '  Settlement: ' . $result['breakdown']['settlement_score'] . PHP_EOL;
    echo '  Density: ' . $result['breakdown']['density_score'] . PHP_EOL;
    $totalVillageScore += $result['score'];
}
echo 'Average Village Score: ' . ($umkms->count() > 0 ? $totalVillageScore / $umkms->count() : 0) . PHP_EOL;
