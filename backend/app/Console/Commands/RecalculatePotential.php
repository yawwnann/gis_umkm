<?php

namespace App\Console\Commands;

use App\Services\PotentialAnalysisService;
use Illuminate\Console\Command;

class RecalculatePotential extends Command
{
    protected $signature = 'umkm:recalculate-potential {--umkm= : Recalculate for specific UMKM ID}';

    protected $description = 'Recalculate economic potential scores for all UMKM';

    public function handle(PotentialAnalysisService $service): int
    {
        $umkmId = $this->option('umkm');

        if ($umkmId) {
            $umkm = \App\Models\Umkm::findOrFail($umkmId);
            $result = $service->calculateForUmkm($umkm);

            $umkm->update([
                'potential_score' => $result['score'],
                'potential_level' => $result['level'],
            ]);

            $this->info("UMKM #{$umkmId} updated:");
            $this->table(
                ['Score', 'Level', 'Road', 'Trading', 'Settlement', 'School', 'Gov', 'Density'],
                [[
                    $result['score'],
                    $result['level']->value ?? $result['level'],
                    $result['breakdown']['road_score'],
                    $result['breakdown']['trading_score'],
                    $result['breakdown']['settlement_score'],
                    $result['breakdown']['school_score'],
                    $result['breakdown']['gov_score'],
                    $result['breakdown']['density_score'],
                ]]
            );

            return Command::SUCCESS;
        }

        $this->info('Recalculating potential scores for all UMKM...');
        $count = $service->recalculateAll();
        $this->info("Done! Updated {$count} records.");

        return Command::SUCCESS;
    }
}
