<?php

use Illuminate\Support\Facades\Route;
use Modules\UserSubscriptions\Http\Controllers\Admin\SubscriptionTypeController;

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'role:admin|super-admin|support-agent', 'permission:manage-subscriptions'])->group(function () {
    Route::name('subscription-types.')->prefix('subscription-types')->group(function () {
        Route::get('/', [SubscriptionTypeController::class, 'index'])->name('index');
        Route::post('/', [SubscriptionTypeController::class, 'store'])->name('store');
        Route::put('{id}', [SubscriptionTypeController::class, 'update'])->name('update');
        Route::delete('{id}', [SubscriptionTypeController::class, 'destroy'])->name('destroy');
        Route::post('{id}/toggle-active', [SubscriptionTypeController::class, 'toggleActive'])->name('toggle-active');
    });
});
