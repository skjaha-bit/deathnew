<?php

use Illuminate\Support\Facades\Route;
use Modules\Insurance\Http\Controllers\InsuranceController;

Route::middleware('auth:api')->group(function () {
    Route::get('v1/insurance-companies', [InsuranceController::class, 'companies']);
    Route::get('v1/insurance-types', [InsuranceController::class, 'insuranceTypes']);

    Route::prefix('v1/insurance-policies')->group(function () {
        Route::get('/', [InsuranceController::class, 'index']);
        Route::post('/', [InsuranceController::class, 'store']);
        Route::post('update', [InsuranceController::class, 'update']);
        Route::post('delete', [InsuranceController::class, 'destroy']);
        Route::get('{policy}', [InsuranceController::class, 'show']);
        Route::post('contact-request', [InsuranceController::class, 'storeContactRequest']);
        Route::post('documents', [InsuranceController::class, 'storeDocument']);
        Route::post('documents/delete', [InsuranceController::class, 'destroyDocument']);
    });
});
