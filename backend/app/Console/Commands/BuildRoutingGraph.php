<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BuildRoutingGraph extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routing:build-graph';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build an adjacency list routing graph from jalan_sungailiat.geojson';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to build routing graph...');
        
        $geoJsonPath = base_path('data/jalan_sungailiat.geojson');
        
        if (!file_exists($geoJsonPath)) {
            $this->error("File not found: {$geoJsonPath}");
            return Command::FAILURE;
        }

        $this->info('Reading GeoJSON file (this may take a moment)...');
        $geoJson = json_decode(file_get_contents($geoJsonPath), true);
        
        if (!$geoJson || empty($geoJson['features'])) {
            $this->error("Invalid or empty GeoJSON file.");
            return Command::FAILURE;
        }

        $features = $geoJson['features'];
        $this->info(count($features) . ' features loaded.');
        
        // Phase 1: Identify nodes (intersections)
        // A node is created if a coordinate appears more than once across all features,
        // OR if it's the start/end of a LineString.
        $this->info('Phase 1: Identifying nodes...');
        $coordCounts = [];
        $nodes = []; // Mapping "lng,lat" to Node ID
        $nodeIndex = 0;
        
        foreach ($features as $feature) {
            if ($feature['geometry']['type'] !== 'LineString') {
                continue;
            }
            $coords = $feature['geometry']['coordinates'];
            $count = count($coords);
            if ($count < 2) continue;
            
            for ($i = 0; $i < $count; $i++) {
                $coordStr = $coords[$i][0] . ',' . $coords[$i][1];
                
                if (!isset($coordCounts[$coordStr])) {
                    $coordCounts[$coordStr] = 0;
                }
                $coordCounts[$coordStr]++;
                
                // Force start and end to be nodes
                if ($i === 0 || $i === $count - 1) {
                    if (!isset($nodes[$coordStr])) {
                        $nodes[$coordStr] = $nodeIndex++;
                    }
                }
            }
        }
        
        // Promote coordinates with count > 1 to nodes (they are intersections)
        foreach ($coordCounts as $coordStr => $count) {
            if ($count > 1 && !isset($nodes[$coordStr])) {
                $nodes[$coordStr] = $nodeIndex++;
            }
        }
        
        $this->info(count($nodes) . ' nodes identified.');
        
        // Phase 2: Build Edges (Adjacency List)
        $this->info('Phase 2: Building adjacency list...');
        $graph = []; // adjacency list: node_id => [ ['to' => node_id, 'weight' => distance, 'coords' => [[lng,lat],...]] ]
        $edgeCount = 0;
        
        foreach ($features as $feature) {
            if ($feature['geometry']['type'] !== 'LineString') {
                continue;
            }
            
            $coords = $feature['geometry']['coordinates'];
            $count = count($coords);
            if ($count < 2) continue;
            
            $oneway = $feature['properties']['oneway'] ?? null;
            $isOneway = ($oneway === 'yes' || $oneway === 'true' || $oneway === '1');
            
            $segmentStartCoordStr = $coords[0][0] . ',' . $coords[0][1];
            $currentStartNode = $nodes[$segmentStartCoordStr];
            $currentSegmentCoords = [$coords[0]];
            $segmentDistance = 0;
            
            for ($i = 1; $i < $count; $i++) {
                $cCoord = $coords[$i];
                $pCoord = $coords[$i-1];
                
                $cCoordStr = $cCoord[0] . ',' . $cCoord[1];
                $currentSegmentCoords[] = $cCoord;
                
                // Accumulate distance
                $dist = $this->haversineDistance($pCoord[1], $pCoord[0], $cCoord[1], $cCoord[0]);
                $segmentDistance += $dist;
                
                // If we hit a node (intersection or end of line)
                if (isset($nodes[$cCoordStr])) {
                    $currentEndNode = $nodes[$cCoordStr];
                    
                    // Add edge from start to end
                    if (!isset($graph[$currentStartNode])) $graph[$currentStartNode] = [];
                    $graph[$currentStartNode][] = [
                        'to' => $currentEndNode,
                        'weight' => $segmentDistance, // in kilometers
                        'coords' => $currentSegmentCoords
                    ];
                    $edgeCount++;
                    
                    // If not oneway, add reverse edge
                    if (!$isOneway) {
                        if (!isset($graph[$currentEndNode])) $graph[$currentEndNode] = [];
                        $graph[$currentEndNode][] = [
                            'to' => $currentStartNode,
                            'weight' => $segmentDistance,
                            'coords' => array_reverse($currentSegmentCoords)
                        ];
                        $edgeCount++;
                    }
                    
                    // Reset for next segment in this line
                    $currentStartNode = $currentEndNode;
                    $currentSegmentCoords = [$cCoord];
                    $segmentDistance = 0;
                }
            }
        }
        
        $this->info("{$edgeCount} edges created.");
        
        // Phase 3: Save to Storage
        $this->info('Phase 3: Saving graph to storage...');
        
        // We need a way to look up nodes by coordinates, so we'll store both:
        // 1. The Adjacency List (graph)
        // 2. The Node Lookup (coord -> node_id)
        
        // Reverse node lookup for findNearestNode: node_id => [lng, lat]
        $nodeMap = [];
        foreach ($nodes as $coordStr => $id) {
            $parts = explode(',', $coordStr);
            $nodeMap[$id] = [(float)$parts[0], (float)$parts[1]]; // [lng, lat]
        }
        
        $dataToSave = [
            'nodes' => $nodeMap,
            'graph' => $graph
        ];
        
        Storage::put('routing_graph.json', json_encode($dataToSave));
        
        $this->info('Graph successfully saved to storage/app/routing_graph.json');
        return Command::SUCCESS;
    }

    /**
     * Calculate Haversine distance in kilometers
     */
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * asin(sqrt($a));
        
        return $earthRadius * $c;
    }
}
