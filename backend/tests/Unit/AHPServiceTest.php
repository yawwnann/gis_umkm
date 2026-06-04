<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\AHPService;

class AHPServiceTest extends TestCase
{
    private AHPService $ahpService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ahpService = new AHPService();
    }

    public function test_can_calculate_weights_and_cr_for_consistent_matrix()
    {
        // 3x3 consistent matrix
        $matrix = [
            [1, 3, 5],
            [1/3, 1, 2],
            [1/5, 1/2, 1]
        ];

        $weights = $this->ahpService->calculateWeights($matrix);
        
        $this->assertCount(3, $weights);
        $this->assertEqualsWithDelta(0.637, $weights[0], 0.05);
        $this->assertEqualsWithDelta(0.258, $weights[1], 0.05);
        $this->assertEqualsWithDelta(0.105, $weights[2], 0.05);

        $cr = $this->ahpService->calculateConsistencyRatio($matrix, $weights);
        $this->assertTrue($this->ahpService->isConsistent($cr));
        $this->assertLessThanOrEqual(0.1, $cr);
    }

    public function test_can_detect_inconsistent_matrix()
    {
        // 3x3 highly inconsistent matrix
        $matrix = [
            [1, 9, 1/9],
            [1/9, 1, 9],
            [9, 1/9, 1]
        ];

        $weights = $this->ahpService->calculateWeights($matrix);
        $cr = $this->ahpService->calculateConsistencyRatio($matrix, $weights);
        
        $this->assertFalse($this->ahpService->isConsistent($cr));
        $this->assertGreaterThan(0.1, $cr);
    }
}
