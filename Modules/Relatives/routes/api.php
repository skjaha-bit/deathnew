<?php

use Illuminate\Support\Facades\Route;
use Modules\Relatives\Http\Controllers\DeathReportController;
use Modules\Relatives\Http\Controllers\PetController;
use Modules\Relatives\Http\Controllers\RelativeController;
use Modules\Relatives\Http\Controllers\WorkplaceController;

// Public: a relative may not be an app user, so this cannot require auth:api.
// It only ever creates a pending review item - see DeathReportController.
Route::post('v1/death-reports', [DeathReportController::class, 'store']);

Route::prefix('v1/relatives')->middleware('auth:api')->group(function () {
    Route::get('/', [RelativeController::class, 'index']);
    Route::post('/', [RelativeController::class, 'store']);
    Route::get('lookup', [RelativeController::class, 'lookup'])->middleware('throttle:30,1');
    Route::post('update', [RelativeController::class, 'update']);
    Route::get('{relative}', [RelativeController::class, 'show']);
    Route::post('{relative}/delete', [RelativeController::class, 'destroy']);
    Route::post('{relative}/invite', [RelativeController::class, 'invite']);
});

Route::prefix('v1/workplaces')->middleware('auth:api')->group(function () {
    Route::get('/', [WorkplaceController::class, 'index']);
    Route::post('/', [WorkplaceController::class, 'store']);
    Route::get('{workplace}', [WorkplaceController::class, 'show']);
    Route::post('update', [WorkplaceController::class, 'update']);
    Route::post('{workplace}/delete', [WorkplaceController::class, 'destroy']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('v1/pet-guardians', [PetController::class, 'guardians']);
    Route::get('v1/pet-insurance-providers', [PetController::class, 'insuranceProviders']);

    Route::prefix('v1/pets')->group(function () {
        Route::get('/', [PetController::class, 'index']);
        Route::post('/', [PetController::class, 'store']);
        Route::post('update', [PetController::class, 'update']);
        Route::post('delete', [PetController::class, 'destroy']);
        Route::get('{pet}', [PetController::class, 'show']);
    });
});
