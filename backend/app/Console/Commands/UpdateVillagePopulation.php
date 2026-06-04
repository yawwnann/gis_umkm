<?php

namespace App\Console\Commands;

use App\Models\Village;
use Illuminate\Console\Command;

class UpdateVillagePopulation extends Command
{
    protected $signature = 'village:update-population';

    protected $description = 'Update village population and compute density from population_data.json';

    public function handle(): int
    {
        $jsonPath = base_path('data/population_data.json');

        if (!file_exists($jsonPath)) {
            $this->error("File not found: {$jsonPath}");
            return Command::FAILURE;
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON: ' . json_last_error_msg());
            return Command::FAILURE;
        }

        $villages = $data['villages'] ?? [];
        if (empty($villages)) {
            $this->error('No village data found in JSON.');
            return Command::FAILURE;
        }

        $this->info("Updating population for {$data['kecamatan']['nama']} ({$data['kecamatan']['kode']})...");
        $this->newLine();

        $updated = 0;
        $notFound = [];

        foreach ($villages as $v) {
            $kode = $v['kode_desa_kelurahan'];
            $population = $v['total'];
            $name = $v['nama_desa_kelurahan'];

            $village = Village::where('name', 'ilike', $name)->first();

            if (!$village) {
                $notFound[] = $name;
                $this->line("  [SKIP] {$name}: not found in database");
                continue;
            }

            // area_km2 already set by importer (LUAS field) if re-imported,
            // but we also compute density here regardless
            $areaKm2 = $village->area_km2;

            $density = null;
            if ($population && $areaKm2 && $areaKm2 > 0) {
                $density = $population / $areaKm2;
            }

            $village->updateQuietly([
                'population' => $population,
                'density' => $density,
            ]);

            $updated++;
            $densityStr = $density !== null ? round($density, 1) . '/km²' : 'N/A';
            $this->line("  [OK]   {$name}: pop={$population}, area=" . round($areaKm2 ?? 0, 2) . " km², density={$densityStr}");
        }

        $this->newLine();

        if ($updated > 0) {
            $this->info("Total updated: {$updated} villages.");
        }

        if (!empty($notFound)) {
            $this->warn("Not found in DB (" . count($notFound) . "): " . implode(', ', $notFound));
        }

        return Command::SUCCESS;
    }
}
