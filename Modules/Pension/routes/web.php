<?php

use Illuminate\Support\Facades\Route;
use Modules\Pension\Http\Controllers\Admin\PensionFundController;

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'role:admin|super-admin|support-agent', 'permission:manage-reference-data'])->group(function () {
    Route::name('pension-funds.')->prefix('pension-funds')->group(function () {
        Route::get('/', [PensionFundController::class, 'index'])->name('index');
        Route::post('/', [PensionFundController::class, 'store'])->name('store');
        Route::put('{id}', [PensionFundController::class, 'update'])->name('update');
        Route::delete('{id}', [PensionFundController::class, 'destroy'])->name('destroy');
        Route::post('{id}/toggle-active', [PensionFundController::class, 'toggleActive'])->name('toggle-active');
    });
});
