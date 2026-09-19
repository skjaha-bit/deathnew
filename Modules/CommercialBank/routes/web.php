<?php

use Illuminate\Support\Facades\Route;
use Modules\CommercialBank\Http\Controllers\Admin\AccountTypeController;
use Modules\CommercialBank\Http\Controllers\Admin\BankController;
use Modules\CommercialBank\Http\Controllers\Admin\BondTypeController;
use Modules\CommercialBank\Http\Controllers\Admin\FundTypeController;
use Modules\CommercialBank\Http\Controllers\Admin\LoanTypeController;

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'role:admin|super-admin|support-agent', 'permission:manage-reference-data'])->group(function () {
    $lookups = [
        'banks' => BankController::class,
        'loan-types' => LoanTypeController::class,
        'account-types' => AccountTypeController::class,
        'bond-types' => BondTypeController::class,
        'fund-types' => FundTypeController::class,
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
