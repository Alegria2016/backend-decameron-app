<?php

use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\HotelRoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Versión 1 de la API
Route::prefix('v1')->group(function () {
    // Hoteles
    Route::apiResource('hotels', HotelController::class);
    
    // Habitaciones de hotel
    Route::prefix('hotels/{hotel}')->group(function () {
        Route::apiResource('rooms', HotelRoomController::class)
            ->names([
                'index' => 'api.v1.hotels.rooms.index',
                'store' => 'api.v1.hotels.rooms.store',
                'show' => 'api.v1.hotels.rooms.show',
                'update' => 'api.v1.hotels.rooms.update',
                'destroy' => 'api.v1.hotels.rooms.destroy',
            ]);
    });
});

/// Obtener tipos de habitación y acomodaciones (también en v1)
Route::prefix('v1')->group(function () {
    Route::get('/room-types', function () {
        return response()->json(\App\Models\RoomType::with('accommodations')->get());
    });

    Route::get('/accommodations', function () {
        return response()->json(\App\Models\Accommodation::all());
    });
});