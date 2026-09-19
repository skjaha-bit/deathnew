<?php

use Illuminate\Support\Facades\Route;
use Modules\AfterMyDay\Http\Controllers\AfterMyDayController;

Route::middleware('auth:api')->group(function () {
    Route::get('v1/funeral-ceremony-types', [AfterMyDayController::class, 'ceremonyTypes']);
    Route::get('v1/funeral-arrangement-types', [AfterMyDayController::class, 'arrangementTypes']);
    Route::get('v1/funeral-service-types', [AfterMyDayController::class, 'funeralServiceTypes']);

    Route::get('v1/funeral', [AfterMyDayController::class, 'showFuneral']);
    Route::post('v1/funeral', [AfterMyDayController::class, 'updateFuneral']);

    Route::get('v1/farewell-drink', [AfterMyDayController::class, 'showFarewellDrink']);
    Route::post('v1/farewell-drink', [AfterMyDayController::class, 'updateFarewellDrink']);

    Route::get('v1/death-notice', [AfterMyDayController::class, 'show']);
    Route::put('v1/death-notice', [AfterMyDayController::class, 'update']);
    Route::post('v1/death-notice/photo', [AfterMyDayController::class, 'uploadPhoto']);

    Route::get('v1/lifetime-achievement', [AfterMyDayController::class, 'showLifetimeAchievement']);
    Route::post('v1/lifetime-achievement', [AfterMyDayController::class, 'updateLifetimeAchievement']);
    Route::post('v1/lifetime-achievement/resume', [AfterMyDayController::class, 'uploadLifetimeAchievementResume']);

    Route::prefix('v1/memories')->group(function () {
        Route::get('/', [AfterMyDayController::class, 'memoriesIndex']);
        Route::post('/', [AfterMyDayController::class, 'storeMemory']);
        Route::post('update', [AfterMyDayController::class, 'updateMemory']);
        Route::post('delete', [AfterMyDayController::class, 'destroyMemory']);
        Route::get('{memory}', [AfterMyDayController::class, 'showMemory']);
    });
});
