<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\PersonLookupController;

Route::prefix('v1/auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('eid/start', [AuthController::class, 'electronicIdStart'])->middleware('throttle:10,1');
    Route::post('eid/poll', [AuthController::class, 'electronicIdPoll'])->middleware('throttle:120,1');

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

// Public: no token needed. Returns a person's full address, so it keeps a tight per-IP throttle.
Route::get('v1/person-lookup', [PersonLookupController::class, 'show'])->middleware('throttle:30,1');

Route::prefix('v1/profile')->middleware('auth:api')->group(function () {
    Route::get('/', [AuthController::class, 'show']);
    Route::put('/', [AuthController::class, 'update']);
    Route::post('avatar', [AuthController::class, 'uploadAvatar']);
});
