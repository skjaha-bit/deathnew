<?php

use Illuminate\Support\Facades\Route;
use Modules\Overview\Http\Controllers\OverviewController;

Route::middleware('auth:api')->group(function () {
    Route::get('v1/overview', [OverviewController::class, 'index']);
});
