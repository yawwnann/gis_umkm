<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VillageResource;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VillageController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Village::withCount('umkms');

        if ($request->has('search')) {
            $query->where('name', 'ilike', "%{$request->search}%");
        }

        $perPage = $request->input('per_page', 25);
        $villages = $query->paginate($perPage);

        return VillageResource::collection($villages);
    }

    public function show(Village $village): VillageResource
    {
        $village->loadCount('umkms');

        return new VillageResource($village);
    }
}