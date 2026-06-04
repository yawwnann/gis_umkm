<?php

namespace App\Importers;

use App\Models\Umkm;
use App\Models\Village;

class UmkmImporter extends BaseGeoJSONImporter
{
    public function __construct()
    {
        parent::__construct('data/umkm2.geojson');
    }

    public function import(): int
    {
        $count = 0;

        foreach ($this->features as $feature) {
            $props = $feature['properties'] ?? [];
            $geom = $feature['geometry'] ?? [];

            $name = $props['nama_proyek'] ?? $props['Nama Perusahaan'] ?? $props['name'] ?? null;
            $owner = $props['Nama Perusahaan'] ?? 'Unknown';
            $address = $props['Alamat Usaha'] ?? '';
            $kelurahan = $props['kelurahan_usaha'] ?? null;
            $kbli = $props['Judul Kbli'] ?? $props['Kategori'] ?? 'Lainnya';
            $skala = $props['Uraian Skala Usaha'] ?? 'Usaha Mikro';

            if (empty($name)) {
                continue;
            }

            // Find village by name
            $villageId = null;
            if ($kelurahan) {
                $village = Village::where('name', 'ilike', '%' . $kelurahan . '%')->first();
                $villageId = $village?->id;
            }

            // Extract coordinates
            $coords = $this->extractCoordinates($geom);
            $lng = $props['longitude'] ?? $coords['lng'];
            $lat = $props['latitude'] ?? $coords['lat'];

            // Normalize category
            $category = $this->normalizeCategory($kbli);

            // Check if already exists (by name and owner)
            $exists = Umkm::where('name', $name)
                ->where('owner', $owner)
                ->exists();

            if ($exists) {
                continue;
            }

            Umkm::create([
                'name' => $name,
                'owner' => $owner,
                'category' => $category,
                'address' => $address,
                'latitude' => $lat,
                'longitude' => $lng,
                'geom' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $lng, (float) $lat],
                ],
                'village_id' => $villageId,
            ]);

            $count++;
        }

        return $count;
    }

    private function normalizeCategory(string $kbli): string
    {
        $kbli = strtolower($kbli);

        $categories = [
            'restoran' => 'Restoran',
            'warung makan' => 'Warung Makan',
            'kedai makanan' => 'Kedai Makanan',
            'kedai minuman' => 'Kedai Minuman',
            'industri roti' => 'Industri Roti & Kue',
            'industri kue' => 'Industri Kue Basah',
            'industri kue kering' => 'Industri Kue Kering',
            'industri cokelat' => 'Industri Cokelat',
            'industri makanan' => 'Industri Makanan',
            'perdagangan eceran' => 'Perdagangan Eceran',
            'makanan lainnya' => 'Makanan Lainnya',
            'rumah/warung makan' => 'Warung Makan',
        ];

        foreach ($categories as $key => $label) {
            if (str_contains($kbli, $key)) {
                return $label;
            }
        }

        return 'Lainnya';
    }
}
