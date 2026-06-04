<?php

namespace App\Console\Commands;

use App\Models\Village;
use Illuminate\Console\Command;

class ComputeVillageDensity extends Command
{
    protected $signature = 'village:compute-density';

    protected $description = 'Compute population density for all villages (population / area_km2)';

    public function handle(): int
    {
        $villages = Village::whereNotNull('population')
            ->whereNotNull('area_km2')
            ->where('area_km2', '>', 0)
            ->get();

        if ($villages->isEmpty()) {
            $this->warn('No villages with both population and area_km2 set. Run `village:update-population` first.');
            return Command::SUCCESS;
        }

        $this->info("Computing density for {$villages->count()} villages...");
        $this->newLine();

        foreach ($villages as $village) {
            $density = $village->population / $village->area_km2;
            $village->updateQuietly(['density' => $density]);
            $this->line("  [OK] {$village->name}: {$village->population} / " . round($village->area_km2, 4) . " = " . round($density, 1) . "/km²");
        }

        $maxDensity = $villages->max(fn($v) => $v->density);
        $minDensity = $villages->min(fn($v) => $v->density);

        $this->newLine();
        $this->info("Done. Max density: " . round($maxDensity, 1) . "/km² | Min density: " . round($minDensity, 1) . "/km²");

        return Command::SUCCESS;
    }
}
