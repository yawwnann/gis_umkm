<?php

namespace App\Observers;

use App\Models\Umkm;
use App\Services\PotentialAnalysisService;

class UmkmObserver
{
    private PotentialAnalysisService $service;

    public function __construct()
    {
        $this->service = new PotentialAnalysisService();
    }

    /**
     * Automatically recalculate potential score when a new UMKM is created.
     */
    public function created(Umkm $umkm): void
    {
        $this->recalculate($umkm);
    }

    /**
     * Recalculate potential score only when location-related fields change.
     * Other field updates (name, owner, category) do not affect the score.
     */
    public function updated(Umkm $umkm): void
    {
        $locationFields = ['geom', 'village_id', 'latitude', 'longitude'];

        if ($umkm->wasChanged($locationFields)) {
            $this->recalculate($umkm);
        }
    }

    private function recalculate(Umkm $umkm): void
    {
        // Skip if geom or location fields are not set yet
        if (!$umkm->geom) {
            return;
        }

        $result = $this->service->calculateForUmkm($umkm);

        $umkm->updateQuietly([
            'potential_score' => $result['score'],
            'potential_level' => $result['level'],
        ]);
    }
}
