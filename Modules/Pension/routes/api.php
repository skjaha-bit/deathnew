<?php

use Illuminate\Support\Facades\Route;
use Modules\Pension\Http\Controllers\PensionController;

Route::middleware('auth:api')->group(function () {
    Route::get('v1/pension-funds', [PensionController::class, 'fundOptions']);

    Route::prefix('v1/pension-entries')->group(function () {
        Route::get('/', [PensionController::class, 'index']);
        Route::post('/', [PensionController::class, 'store']);
        Route::get('{pensionEntry}', [PensionController::class, 'show']);
        Route::put('{pensionEntry}', [PensionController::class, 'update']);
        Route::delete('{pensionEntry}', [PensionController::class, 'destroy']);
    });

    Route::prefix('v1/pension-fund-inquiries')->group(function () {
        Route::get('/', [PensionController::class, 'inquiriesIndex']);
        Route::post('/', [PensionController::class, 'inquiriesStore']);
    });
});
