<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use SplPriorityQueue;

class RoutingService
{
    private $nodes = [];
    private $graph = [];
    private $isLoaded = false;

    public function __construct()
    {
        $this->loadGraph();
    }

    private function loadGraph()
    {
        if ($this->isLoaded) {
            return;
        }

        if (!Storage::exists('routing_graph.json')) {
            throw new \Exception("Routing graph not found. Please run 'php artisan routing:build-graph' first.");
        }

        $data = json_decode(Storage::get('routing_graph.json'), true);
        $this->nodes = $data['nodes'];
        $this->graph = $data['graph'];
        $this->isLoaded = true;
    }

    /**
     * Find the nearest node ID to a given lat/lng
     */
    public function findNearestNode($lat, $lng)
    {
        $minDist = PHP_FLOAT_MAX;
        $nearestId = null;

        // O(N) scan, N=~10k is fast enough in PHP (< 10ms)
        foreach ($this->nodes as $id => $coord) {
            $nLng = $coord[0];
            $nLat = $coord[1];
            
            // Fast approximation using Pythagorean theorem on equirectangular projection
            $x = ($lng - $nLng) * cos(deg2rad(($lat + $nLat) / 2));
            $y = ($lat - $nLat);
            $distSq = $x*$x + $y*$y;
            
            if ($distSq < $minDist) {
                $minDist = $distSq;
                $nearestId = $id;
            }
        }

        return $nearestId;
    }

    /**
     * Calculate route from A to B using Dijkstra's Algorithm
     */
    public function calculateRoute($startLat, $startLng, $endLat, $endLng)
    {
        $startNode = $this->findNearestNode($startLat, $startLng);
        $endNode = $this->findNearestNode($endLat, $endLng);

        if ($startNode === null || $endNode === null) {
            return null;
        }

        if ($startNode === $endNode) {
            return [
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => [[$startLng, $startLat], [$endLng, $endLat]]
                ],
                'distance' => 0,
                'duration' => 0,
                'distance_km' => 0,
                'duration_minutes' => 0
            ];
        }

        // Dijkstra implementation
        $distances = [];
        $previous = [];
        $pq = new SplPriorityQueue();
        
        // Setup Priority Queue to behave as a Min-Heap (smaller distance = higher priority)
        $pq->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
        
        // Initialize distances
        foreach ($this->nodes as $id => $c) {
            $distances[$id] = INF;
            $previous[$id] = null;
        }
        
        $distances[$startNode] = 0;
        
        // SplPriorityQueue in PHP extracts largest priority first. 
        // We negate the distance so smaller distance is extracted first.
        $pq->insert($startNode, 0);

        while (!$pq->isEmpty()) {
            $extracted = $pq->extract();
            $u = $extracted['data'];
            $currentDist = -$extracted['priority'];

            // If we found the target, we can break early
            if ($u === $endNode) {
                break;
            }

            // Skip if we found a shorter path previously
            if ($currentDist > $distances[$u]) {
                continue;
            }

            if (!isset($this->graph[$u])) {
                continue; // dead end
            }

            foreach ($this->graph[$u] as $edge) {
                $v = $edge['to'];
                $weight = $edge['weight'];
                $alt = $distances[$u] + $weight;

                if ($alt < $distances[$v]) {
                    $distances[$v] = $alt;
                    $previous[$v] = ['node' => $u, 'coords' => $edge['coords']];
                    $pq->insert($v, -$alt);
                }
            }
        }

        // Reconstruct path
        if ($distances[$endNode] === INF) {
            return null; // No route found
        }

        $pathCoords = [];
        $u = $endNode;
        $totalDistanceKm = 0;

        while (isset($previous[$u])) {
            $edgeData = $previous[$u];
            // The edge coords are from parent to U.
            // Since we are backtracking from End to Start, we prepend them reversed, 
            // but we need to match them properly.
            // Actually, edge['coords'] goes from parent -> U. 
            // So if we collect them into an array and then reverse the array of edges, it's easier.
            
            $u = $edgeData['node'];
        }

        // Second pass: traverse from start to end using the discovered path
        $pathNodeSequence = [];
        $curr = $endNode;
        while ($curr !== null) {
            array_unshift($pathNodeSequence, $curr);
            $curr = $previous[$curr] ? $previous[$curr]['node'] : null;
        }

        // Build coordinate list
        $finalCoords = [];
        // Add exact start coordinate
        $finalCoords[] = [(float)$startLng, (float)$startLat];
        
        for ($i = 0; $i < count($pathNodeSequence) - 1; $i++) {
            $u = $pathNodeSequence[$i];
            $v = $pathNodeSequence[$i + 1];
            
            // Find edge data in graph
            foreach ($this->graph[$u] as $edge) {
                if ($edge['to'] === $v) {
                    $totalDistanceKm += $edge['weight'];
                    // Append edge coords (skip first coord if it matches the last added to avoid duplicates)
                    $edgeCoords = $edge['coords'];
                    foreach ($edgeCoords as $c) {
                        $last = end($finalCoords);
                        if ($last[0] !== $c[0] || $last[1] !== $c[1]) {
                            $finalCoords[] = $c;
                        }
                    }
                    break;
                }
            }
        }
        
        // Add exact end coordinate
        $last = end($finalCoords);
        if ($last[0] != $endLng || $last[1] != $endLat) {
            $finalCoords[] = [(float)$endLng, (float)$endLat];
        }

        // Estimate duration (assume 30 km/h)
        $speedKmH = 30;
        $durationHours = $totalDistanceKm / $speedKmH;
        $durationMinutes = $durationHours * 60;
        
        return [
            'geometry' => [
                'type' => 'LineString',
                'coordinates' => $finalCoords
            ],
            'distance' => round($totalDistanceKm * 1000), // meters
            'duration' => round($durationMinutes * 60), // seconds
            'distance_km' => round($totalDistanceKm, 2),
            'duration_minutes' => round($durationMinutes, 1)
        ];
    }
}
