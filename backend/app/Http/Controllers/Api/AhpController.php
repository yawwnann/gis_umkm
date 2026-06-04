<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AhpController extends Controller
{
    private $ahpService;

    public function __construct(\App\Services\AHPService $ahpService)
    {
        $this->ahpService = $ahpService;
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'matrix' => 'required|array|size:6',
            'matrix.*' => 'required|array|size:6'
        ]);

        $matrix = $request->matrix;

        try {
            $weights = $this->ahpService->calculateWeights($matrix);
            $cr = $this->ahpService->calculateConsistencyRatio($matrix, $weights);
            $isConsistent = $this->ahpService->isConsistent($cr);

            return response()->json([
                'weights' => $weights,
                'consistency_ratio' => $cr,
                'is_consistent' => $isConsistent,
                'warning' => !$isConsistent ? 'Matrix is inconsistent (CR > 0.1). Please adjust your pairwise comparisons.' : null
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function save(Request $request)
    {
        $request->validate([
            'weights' => 'required|array|size:6',
            'weights.*' => 'required|numeric'
        ]);

        $criteriaNames = ['road', 'trading', 'settlement', 'education', 'government', 'population_density'];
        
        foreach ($request->weights as $index => $weightValue) {
            if (isset($criteriaNames[$index])) {
                \App\Models\AnalysisWeight::updateOrCreate(
                    ['criteria' => $criteriaNames[$index]],
                    ['weight' => $weightValue]
                );
            }
        }

        return response()->json(['message' => 'AHP weights saved successfully.']);
    }
}
