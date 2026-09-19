<?php

use Illuminate\Support\Facades\Route;
use Modules\Property\Http\Controllers\Admin\PropertyTypeController;

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'role:admin|super-admin|support-agent', 'permission:manage-reference-data'])->group(function () {
    Route::name('property-types.')->prefix('property-types')->group(function () {
        Route::get('/', [PropertyTypeController::class, 'index'])->name('index');
        Route::post('/', [PropertyTypeController::class, 'store'])->name('store');
        Route::put('{id}', [PropertyTypeController::class, 'update'])->name('update');
        Route::delete('{id}', [PropertyTypeController::class, 'destroy'])->name('destroy');
        Route::post('{id}/toggle-active', [PropertyTypeController::class, 'toggleActive'])->name('toggle-active');
    });
});
