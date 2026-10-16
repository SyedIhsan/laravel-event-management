<?php

use App\Http\Controllers\Api\AttendeeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// login routes
Route::post('/login', [AuthController::class, 'login']);

// public routes
Route::apiResource('events', EventController::class)
    ->only(['index', 'show']);
Route::apiResource('events.attendees', AttendeeController::class)
    ->scoped()->only(['index', 'show']);

// protected routes
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // logout routes
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('events', EventController::class)
        ->only(['store', 'update', 'destroy']);
    Route::apiResource('events.attendees', AttendeeController::class)
        ->scoped()->only(['store', 'destroy']);
});
