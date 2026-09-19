<?php

use Illuminate\Support\Facades\Route;
use Modules\CommercialBank\Http\Controllers\CommercialBankController;

Route::middleware('auth:api')->group(function () {
    Route::get('v1/banks', [CommercialBankController::class, 'banks']);
    Route::get('v1/loan-types', [CommercialBankController::class, 'loanTypes']);
    Route::get('v1/account-types', [CommercialBankController::class, 'accountTypes']);
    Route::get('v1/bond-types', [CommercialBankController::class, 'bondTypes']);
    Route::get('v1/fund-types', [CommercialBankController::class, 'fundTypes']);

    Route::prefix('v1/commercial-banks')->group(function () {
        Route::get('/', [CommercialBankController::class, 'index']);
        Route::post('/', [CommercialBankController::class, 'store']);
        Route::post('update', [CommercialBankController::class, 'update']);
        Route::post('delete', [CommercialBankController::class, 'destroy']);
        Route::get('{userCommercialBank}', [CommercialBankController::class, 'show']);
    });
});
