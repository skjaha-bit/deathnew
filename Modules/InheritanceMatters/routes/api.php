<?php

use Illuminate\Support\Facades\Route;
use Modules\InheritanceMatters\Http\Controllers\InheritanceMatterController;

Route::middleware('auth:api')->group(function () {
    Route::get('v1/inheritance-matters', [InheritanceMatterController::class, 'show']);
    Route::post('v1/inheritance-matters', [InheritanceMatterController::class, 'save']);
});
