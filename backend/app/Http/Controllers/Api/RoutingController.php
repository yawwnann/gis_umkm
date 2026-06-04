<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RoutingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoutingController extends Controller
{
    private $routingService;

    public function __construct(RoutingService $routingService)
    {
        $this->routingService = $routingService;
    }

    public function route(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start' => ['required', 'array'],
            'start.lat' => ['required', 'numeric', 'between:-90,90'],
            'start.lng' => ['required', 'numeric', 'between:-180,180'],
            'end' => ['required', 'array'],
            'end.lat' => ['required', 'numeric', 'between:-90,90'],
            'end.lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $startLat = $validated['start']['lat'];
        $startLng = $validated['start']['lng'];
        $endLat = $validated['end']['lat'];
        $endLng = $validated['end']['lng'];

        try {
            $result = $this->routingService->calculateRoute($startLat, $startLng, $endLat, $endLng);

            if (!$result) {
                return response()->json([
                    'message' => 'Route not found between these points.',
                ], 404);
            }

            return response()->json([
                'route' => $result['geometry'],
                'summary' => [
                    'distance_km' => $result['distance_km'],
                    'duration_min' => $result['duration_minutes']
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Routing engine error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function nearest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        try {
            $nodeId = $this->routingService->findNearestNode($validated['lat'], $validated['lng']);
            
            if ($nodeId === null) {
                return response()->json([
                    'message' => 'Nearest point not found',
                ], 404);
            }

            return response()->json([
                'data' => [
                    'node_id' => $nodeId
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Routing engine error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
