<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\Http\Controllers\Admin\SubscriptionPlanController;

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'role:admin|super-admin|support-agent', 'permission:manage-subscriptions'])->group(function () {
    Route::name('subscription-plans.')->prefix('subscription-plans')->group(function () {
        Route::get('/', [SubscriptionPlanController::class, 'index'])->name('index');
        Route::post('/', [SubscriptionPlanController::class, 'store'])->name('store');
        Route::put('{id}', [SubscriptionPlanController::class, 'update'])->name('update');
        Route::delete('{id}', [SubscriptionPlanController::class, 'destroy'])->name('destroy');
        Route::post('{id}/toggle-active', [SubscriptionPlanController::class, 'toggleActive'])->name('toggle-active');
    });
});
