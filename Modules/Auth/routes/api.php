<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;

Route::prefix('v1/auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('eid/start', [AuthController::class, 'electronicIdStart'])->middleware('throttle:10,1');
    Route::post('eid/poll', [AuthController::class, 'electronicIdPoll'])->middleware('throttle:120,1');

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::prefix('v1/profile')->middleware('auth:api')->group(function () {
    Route::get('/', [AuthController::class, 'show']);
    Route::put('/', [AuthController::class, 'update']);
    Route::post('avatar', [AuthController::class, 'uploadAvatar']);
});
