<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\Http\Controllers\SubscriptionController;

Route::prefix('v1/subscription-plans')->group(function () {
    Route::get('/', [SubscriptionController::class, 'plans']);
});

Route::prefix('v1/subscriptions')->middleware('auth:api')->group(function () {
    Route::get('current', [SubscriptionController::class, 'current']);
    Route::post('select', [SubscriptionController::class, 'select']);
});
