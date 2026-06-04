<?php

namespace App\Console\Commands;

use App\Models\Village;
use Illuminate\Console\Command;

class SyncVillageArea extends Command
{
    protected $signature = 'village:sync-area';

    protected $description = 'Sync area_km2 from batas_wilayah.geojson LUAS field into existing villages';

    public function handle(): int
    {
        $geoPath = base_path('data/batas_wilayah.geojson');

        if (!file_exists($geoPath)) {
            $this->error("File not found: {$geoPath}");
            return Command::FAILURE;
        }

        $data = json_decode(file_get_contents($geoPath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid GeoJSON: ' . json_last_error_msg());
            return Command::FAILURE;
        }

        $features = $data['features'] ?? [];
        $this->info("Processing {$features->count()} features...");
        $this->newLine();

        $updated = 0;

        foreach ($features as $feature) {
            $props = $feature['properties'] ?? [];
            $name = $props['NAMOBJ'] ?? null;
            $luas = $props['LUAS'] ?? null;

            if (!$name || strlen($name) < 3) {
                continue;
            }

            $village = Village::where('name', 'ilike', $name)->first();
            if (!$village) {
                continue;
            }

            if ($luas !== null && $luas > 0) {
                $village->updateQuietly(['area_km2' => $luas]);
                $this->line("  [OK]   {$name}: area_km2 = {$luas}");
                $updated++;
            } else {
                $this->line("  [SKIP] {$name}: LUAS = " . ($luas ?? 'null'));
            }
        }

        $this->newLine();
        $this->info("Updated {$updated} villages with area_km2.");

        return Command::SUCCESS;
    }
}