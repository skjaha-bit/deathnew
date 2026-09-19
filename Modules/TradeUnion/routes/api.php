<?php

use Illuminate\Support\Facades\Route;
use Modules\TradeUnion\Http\Controllers\TradeUnionController;

Route::middleware('auth:api')->group(function () {
    Route::get('v1/trade-unions', [TradeUnionController::class, 'options']);

    Route::prefix('v1/user-trade-unions')->group(function () {
        Route::get('/', [TradeUnionController::class, 'index']);
        Route::post('/', [TradeUnionController::class, 'store']);
        Route::post('update', [TradeUnionController::class, 'update']);
        Route::post('delete', [TradeUnionController::class, 'destroy']);
        Route::get('{userTradeUnion}', [TradeUnionController::class, 'show']);
    });
});
