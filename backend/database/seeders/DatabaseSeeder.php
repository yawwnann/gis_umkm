<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Importers\GovernmentFacilityImporter;
use App\Importers\RoadImporter;
use App\Importers\SchoolImporter;
use App\Importers\TourismImporter;
use App\Importers\TradingCenterImporter;
use App\Importers\UmkmImporter;
use App\Importers\VillageImporter;
use App\Importers\SettlementImporter;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin users (idempotent - won't duplicate if exists)
        User::firstOrCreate(
            ['email' => 'admin@gisumkm.test'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
                'role' => UserRole::Admin,
            ]
        );

        User::firstOrCreate(
            ['email' => 'officer@gisumkm.test'],
            [
                'name' => 'Petugas Lapangan',
                'password' => bcrypt('password'),
                'role' => UserRole::FieldOfficer,
            ]
        );

        // Import spatial data from GeoJSON files
        $this->importGeoJSON();
    }

    private function importGeoJSON(): void
    {
        $this->command->info('Importing spatial data from GeoJSON files...');

        // Truncate all spatial tables in dependency order (children first)
        $this->command->info('Truncating existing data...');
        DB::statement('TRUNCATE TABLE umkm_photos RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE umkms RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE tourisms RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE government_facilities RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE schools RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE trading_centers RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE settlements RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE roads RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE villages RESTART IDENTITY CASCADE');

        $importers = [
            'Villages' => VillageImporter::class,
            'Roads' => RoadImporter::class,
            'Settlements' => SettlementImporter::class,
            'Trading Centers' => TradingCenterImporter::class,
            'Schools' => SchoolImporter::class,
            'Government Facilities' => GovernmentFacilityImporter::class,
            'Tourisms' => TourismImporter::class,
            'UMKMs' => UmkmImporter::class,
        ];

        $total = 0;

        foreach ($importers as $name => $importerClass) {
            try {
                $importer = new $importerClass();
                $importer->load();
                $count = $importer->import();

                $total += $count;
                $this->command->info("  [OK] {$name}: {$count} records");
            } catch (\Exception $e) {
                $this->command->error("  [ERROR] {$name}: {$e->getMessage()}");
            }
        }

        $this->command->info("Total imported: {$total} records");
    }
}