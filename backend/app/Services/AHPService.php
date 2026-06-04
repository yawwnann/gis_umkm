<?php

namespace App\Services;

class AHPService
{
    /**
     * Random Index (RI) values for n = 1 to 10
     */
    private const RI = [
        1 => 0.00,
        2 => 0.00,
        3 => 0.58,
        4 => 0.90,
        5 => 1.12,
        6 => 1.24,
        7 => 1.32,
        8 => 1.41,
        9 => 1.45,
        10 => 1.49
    ];

    /**
     * Calculate normalized weights from pairwise comparison matrix
     * 
     * @param array $matrix 2D array representing pairwise comparison (NxN)
     * @return array Calculated priority weights
     */
    public function calculateWeights(array $matrix): array
    {
        $n = count($matrix);
        if ($n === 0) return [];

        // 1. Calculate column sums
        $columnSums = array_fill(0, $n, 0);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $columnSums[$j] += $matrix[$i][$j];
            }
        }

        // 2. Normalize the matrix
        $normalizedMatrix = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $normalizedMatrix[$i][$j] = $matrix[$i][$j] / $columnSums[$j];
            }
        }

        // 3. Calculate weights (average of normalized rows)
        $weights = [];
        for ($i = 0; $i < $n; $i++) {
            $sum = 0;
            for ($j = 0; $j < $n; $j++) {
                $sum += $normalizedMatrix[$i][$j];
            }
            $weights[$i] = $sum / $n;
        }

        return $weights;
    }

    /**
     * Calculate Consistency Ratio (CR)
     * 
     * @param array $matrix Original pairwise comparison matrix
     * @param array $weights Calculated priority weights
     * @return float Consistency Ratio
     * @throws \Exception If matrix size > 10
     */
    public function calculateConsistencyRatio(array $matrix, array $weights): float
    {
        $n = count($matrix);
        if ($n <= 2) {
            return 0.0; // Matrices 1x1 or 2x2 are always consistent
        }

        if ($n > 10) {
            throw new \Exception("AHP Random Index (RI) is only defined for n up to 10 in this implementation.");
        }

        // 1. Multiply original matrix by weights to get Weighted Sum Vector
        $weightedSumVector = array_fill(0, $n, 0);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $weightedSumVector[$i] += $matrix[$i][$j] * $weights[$j];
            }
        }

        // 2. Divide weighted sum vector by weights to get lambda estimates
        $lambdaEstimates = [];
        for ($i = 0; $i < $n; $i++) {
            // Avoid division by zero
            if ($weights[$i] > 0) {
                $lambdaEstimates[$i] = $weightedSumVector[$i] / $weights[$i];
            } else {
                $lambdaEstimates[$i] = 0;
            }
        }

        // 3. Calculate Lambda Max (average of lambda estimates)
        $lambdaMax = array_sum($lambdaEstimates) / $n;

        // 4. Calculate Consistency Index (CI)
        $ci = ($lambdaMax - $n) / ($n - 1);

        // 5. Calculate Consistency Ratio (CR)
        $ri = self::RI[$n];
        $cr = $ci / $ri;

        return $cr;
    }

    /**
     * Determine if the matrix is consistent (CR <= 0.1)
     */
    public function isConsistent(float $cr): bool
    {
        return $cr <= 0.1;
    }
}
