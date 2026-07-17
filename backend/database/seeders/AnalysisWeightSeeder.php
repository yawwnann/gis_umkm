<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnalysisWeightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $weights = [
            ['criteria' => 'trading', 'weight' => 0.35],
            ['criteria' => 'settlement', 'weight' => 0.22],
            ['criteria' => 'education', 'weight' => 0.14],
            ['criteria' => 'government', 'weight' => 0.14],
            ['criteria' => 'population_density', 'weight' => 0.15],
        ];

        foreach ($weights as $weight) {
            \App\Models\AnalysisWeight::updateOrCreate(
                ['criteria' => $weight['criteria']],
                ['weight' => $weight['weight']]
            );
        }
    }
}
