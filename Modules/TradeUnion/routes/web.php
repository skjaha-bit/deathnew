<?php

use Illuminate\Support\Facades\Route;
use Modules\TradeUnion\Http\Controllers\Admin\TradeUnionController;

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'role:admin|super-admin|support-agent', 'permission:manage-reference-data'])->group(function () {
    Route::name('trade-unions.')->prefix('trade-unions')->group(function () {
        Route::get('/', [TradeUnionController::class, 'index'])->name('index');
        Route::post('/', [TradeUnionController::class, 'store'])->name('store');
        Route::put('{id}', [TradeUnionController::class, 'update'])->name('update');
        Route::delete('{id}', [TradeUnionController::class, 'destroy'])->name('destroy');
        Route::post('{id}/toggle-active', [TradeUnionController::class, 'toggleActive'])->name('toggle-active');
    });
});
