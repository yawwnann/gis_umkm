<?php

namespace App\Console\Commands;

use App\Importers\GovernmentFacilityImporter;
use App\Importers\SchoolImporter;
use App\Importers\SettlementImporter;
use App\Importers\TourismImporter;
use App\Importers\TradingCenterImporter;
use App\Importers\UmkmImporter;
use App\Importers\VillageImporter;
use Illuminate\Console\Command;

class ImportGeoJSON extends Command
{
    protected $signature = 'geo:import
                            {type : Type of data to import (villages, settlements, umkms, trading-centers, schools, government-facilities, tourisms, all)}
                            {--force : Force re-import even if data exists}';

    protected $description = 'Import spatial data from GeoJSON files';

    public function handle(): int
    {
        $type = $this->argument('type');
        $force = $this->option('force');

        $importers = [
            'villages' => VillageImporter::class,
            'settlements' => SettlementImporter::class,
            'umkms' => UmkmImporter::class,
            'trading-centers' => TradingCenterImporter::class,
            'schools' => SchoolImporter::class,
            'government-facilities' => GovernmentFacilityImporter::class,
            'tourisms' => TourismImporter::class,
        ];

        if ($type === 'all') {
            $this->info('Importing all spatial data...');
            $total = 0;

            foreach ($importers as $name => $importerClass) {
                $count = $this->importData($importerClass, $name, $force);
                $total += $count;
            }

            $this->info("Import complete! Total: {$total} records.");
            return Command::SUCCESS;
        }

        if (!isset($importers[$type])) {
            $this->error("Unknown type: {$type}");
            $this->info('Available types: ' . implode(', ', array_keys($importers)));
            return Command::FAILURE;
        }

        $count = $this->importData($importers[$type], $type, $force);
        $this->info("Imported {$count} {$type}.");

        return Command::SUCCESS;
    }

    private function importData(string $importerClass, string $name, bool $force): int
    {
        $this->info("Importing {$name}...");

        try {
            $importer = new $importerClass();
            $importer->load();
            $count = $importer->import();

            $this->info("  -> {$count} records imported.");

            return $count;
        } catch (\Exception $e) {
            $this->error("  -> Error: {$e->getMessage()}");
            return 0;
        }
    }
}