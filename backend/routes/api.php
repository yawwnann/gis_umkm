<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\RoutingController;
use App\Http\Controllers\Api\UmkmController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VillageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Public map layers (read-only)
Route::prefix('map')->group(function () {
    Route::get('/umkms', [MapController::class, 'umkms']);
    Route::get('/villages', [MapController::class, 'villages']);
    Route::get('/roads', [MapController::class, 'roads']);
    Route::get('/settlements', [MapController::class, 'settlements']);
    Route::get('/trading-centers', [MapController::class, 'tradingCenters']);
    Route::get('/schools', [MapController::class, 'schools']);
    Route::get('/government-facilities', [MapController::class, 'governmentFacilities']);
    Route::get('/tourisms', [MapController::class, 'tourisms']);
});

// Heatmaps - Publicly accessible
Route::prefix('heatmap')->group(function () {
    Route::get('/umkm', [MapController::class, 'heatmapUmkm']);
    Route::get('/potential', [MapController::class, 'heatmapPotential']);
});

// Routing (OSRM) - Publicly accessible
Route::prefix('routing')->group(function () {
    Route::post('/', [RoutingController::class, 'route']);
    Route::get('/nearest', [RoutingController::class, 'nearest']);
});

// Protected routes
Route::middleware('auth:api')->group(function () {
    // AHP
    Route::prefix('ahp')->group(function () {
        Route::post('/calculate', [\App\Http\Controllers\Api\AhpController::class, 'calculate']);
        Route::post('/save', [\App\Http\Controllers\Api\AhpController::class, 'save']);
    });

    // Map Recalculation
    Route::post('/map/recalculate', function () {
        $service = new \App\Services\PotentialAnalysisService();
        $count = $service->recalculateAll();
        return response()->json(['message' => "Successfully recalculated $count UMKM."]);
    });
    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // UMKM Management
    Route::prefix('umkms')->group(function () {
        Route::get('/', [UmkmController::class, 'index']);
        Route::post('/', [UmkmController::class, 'store']);
        Route::get('/categories', [UmkmController::class, 'categories']);
        Route::get('/{umkm}', [UmkmController::class, 'show']);
        Route::put('/{umkm}', [UmkmController::class, 'update']);
        Route::delete('/{umkm}', [UmkmController::class, 'destroy']);
        Route::post('/{umkm}/photos', [UmkmController::class, 'uploadPhoto']);
    });

    // Villages
    Route::prefix('villages')->group(function () {
        Route::get('/', [VillageController::class, 'index']);
        Route::get('/{village}', [VillageController::class, 'show']);
    });

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'stats']);
        Route::get('/by-village', [DashboardController::class, 'byVillage']);
        Route::get('/by-category', [DashboardController::class, 'byCategory']);
        Route::get('/by-potential', [DashboardController::class, 'byPotential']);
        Route::get('/registrations', [DashboardController::class, 'registrations']);
        Route::get('/analysis', [DashboardController::class, 'analysis']);
    });

    // User Management (Admin only)
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword']);
    })->middleware('role:admin');

});
