<?php

use Illuminate\Support\Facades\Route;
use Modules\Insurance\Http\Controllers\Admin\InsuranceCompanyController;
use Modules\Insurance\Http\Controllers\Admin\InsuranceTypeController;

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'role:admin|super-admin|support-agent', 'permission:manage-reference-data'])->group(function () {
    $lookups = [
        'insurance-companies' => InsuranceCompanyController::class,
        'insurance-types' => InsuranceTypeController::class,
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
