<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UmkmResource;
use App\Models\Umkm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UmkmController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Umkm::with(['village', 'primaryPhoto']);

        // Search
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filters
        if ($request->has('category')) {
            $query->filterByCategory($request->category);
        }

        if ($request->has('village_id')) {
            $query->filterByVillage($request->village_id);
        }

        if ($request->has('potential_level')) {
            $query->filterByPotential($request->potential_level);
        }

        $perPage = $request->input('per_page', 15);
        $umkms = $query->paginate($perPage);

        return UmkmResource::collection($umkms);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'owner' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'village_id' => ['nullable', 'exists:villages,id'],
        ]);

        // Create GeoJSON Point from lat/lng
        $validated['geom'] = [
            'type' => 'Point',
            'coordinates' => [(float) $validated['longitude'], (float) $validated['latitude']],
        ];

        $umkm = Umkm::create($validated);

        return response()->json([
            'message' => 'UMKM created successfully',
            'data' => new UmkmResource($umkm->load(['village', 'primaryPhoto'])),
        ], 201);
    }

    public function show(Umkm $umkm): JsonResponse
    {
        $umkm->load(['village', 'photos']);

        return response()->json([
            'data' => new UmkmResource($umkm),
        ]);
    }

    public function update(Request $request, Umkm $umkm): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'owner' => ['sometimes', 'string', 'max:255'],
            'category' => ['sometimes', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'latitude' => ['sometimes', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'numeric', 'between:-180,180'],
            'village_id' => ['nullable', 'exists:villages,id'],
        ]);

        // Update GeoJSON if coordinates changed
        if ($request->has('latitude') || $request->has('longitude')) {
            $lat = $validated['latitude'] ?? $umkm->latitude;
            $lng = $validated['longitude'] ?? $umkm->longitude;
            $validated['geom'] = [
                'type' => 'Point',
                'coordinates' => [(float) $lng, (float) $lat],
            ];
        }

        $umkm->update($validated);

        return response()->json([
            'message' => 'UMKM updated successfully',
            'data' => new UmkmResource($umkm->load(['village', 'primaryPhoto'])),
        ]);
    }

    public function destroy(Umkm $umkm): JsonResponse
    {
        $umkm->delete();

        return response()->json([
            'message' => 'UMKM deleted successfully',
        ]);
    }

    public function categories(): JsonResponse
    {
        $categories = Umkm::distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return response()->json([
            'data' => $categories,
        ]);
    }

    public function uploadPhoto(Request $request, Umkm $umkm): JsonResponse
    {
        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'is_primary' => ['sometimes', 'boolean'],
        ]);

        // Store the photo
        $path = $request->file('photo')->store('umkm-photos', 'public');

        // Get original name without extension
        $originalName = pathinfo($request->file('photo')->getClientOriginalName(), PATHINFO_FILENAME);

        // If setting as primary, unset other primary photos
        if ($request->boolean('is_primary')) {
            $umkm->photos()->update(['is_primary' => false]);
        }

        $photo = $umkm->photos()->create([
            'filename' => basename($path),
            'original_name' => $originalName,
            'mime_type' => $request->file('photo')->getMimeType(),
            'size' => $request->file('photo')->getSize(),
            'is_primary' => $request->boolean('is_primary', $umkm->photos()->count() === 0),
            'order' => $umkm->photos()->max('order') + 1,
        ]);

        return response()->json([
            'message' => 'Photo uploaded successfully',
            'data' => [
                'id' => $photo->id,
                'url' => $photo->url,
                'is_primary' => $photo->is_primary,
            ],
        ], 201);
    }
}
