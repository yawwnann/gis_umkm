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
            ['criteria' => 'road', 'weight' => 0.30],
            ['criteria' => 'trading', 'weight' => 0.25],
            ['criteria' => 'settlement', 'weight' => 0.15],
            ['criteria' => 'government', 'weight' => 0.10],
            ['criteria' => 'education', 'weight' => 0.10],
            ['criteria' => 'population_density', 'weight' => 0.10],
        ];

        foreach ($weights as $weight) {
            \App\Models\AnalysisWeight::updateOrCreate(
                ['criteria' => $weight['criteria']],
                ['weight' => $weight['weight']]
            );
        }
    }
}
