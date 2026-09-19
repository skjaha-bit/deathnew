<?php

use Illuminate\Support\Facades\Route;
use Modules\Property\Http\Controllers\PropertyController;

Route::middleware('auth:api')->group(function () {
    Route::get('v1/property-types', [PropertyController::class, 'propertyTypes']);
    Route::get('v1/property-loan-types', [PropertyController::class, 'loanTypes']);
    Route::get('v1/property-insurance-providers', [PropertyController::class, 'insuranceProviders']);

    Route::prefix('v1/properties')->group(function () {
        Route::get('/', [PropertyController::class, 'index']);
        Route::post('/', [PropertyController::class, 'store']);
        Route::post('update', [PropertyController::class, 'update']);
        Route::post('delete', [PropertyController::class, 'destroy']);
        Route::get('{property}', [PropertyController::class, 'show']);
    });
});
