<?php

use Illuminate\Support\Facades\Route;
use Modules\UserSubscriptions\Http\Controllers\UserSubscriptionController;

Route::middleware('auth:api')->group(function () {
    Route::get('v1/subscription-types', [UserSubscriptionController::class, 'types']);

    Route::prefix('v1/user-subscriptions')->group(function () {
        Route::get('/', [UserSubscriptionController::class, 'index']);
        Route::post('/', [UserSubscriptionController::class, 'store']);
        Route::post('update', [UserSubscriptionController::class, 'update']);
        Route::post('delete', [UserSubscriptionController::class, 'destroy']);
        Route::get('{userSubscription}', [UserSubscriptionController::class, 'show']);
    });
});
