<?php

use Illuminate\Support\Facades\Route;
use Modules\AfterMyDay\Http\Controllers\Admin\ArrangementTypeController;
use Modules\AfterMyDay\Http\Controllers\Admin\CeremonyTypeController;
use Modules\AfterMyDay\Http\Controllers\Admin\FuneralServiceTypeController;

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'role:admin|super-admin|support-agent', 'permission:manage-reference-data'])->group(function () {
    $lookups = [
        'ceremony-types' => CeremonyTypeController::class,
        'arrangement-types' => ArrangementTypeController::class,
        'funeral-service-types' => FuneralServiceTypeController::class,
    ];

    foreach ($lookups as $uri => $controller) {
        Route::name($uri.'.')->prefix($uri)->group(function () use ($controller) {
            Route::get('/', [$controller, 'index'])->name('index');
            Route::post('/', [$controller, 'store'])->name('store');
            Route::put('{id}', [$controller, 'update'])->name('update');
            Route::delete('{id}', [$controller, 'destroy'])->name('destroy');
            Route::post('{id}/toggle-active', [$controller, 'toggleActive'])->name('toggle-active');
        });
    }
});
