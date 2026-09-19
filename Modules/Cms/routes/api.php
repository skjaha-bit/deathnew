<?php

use Illuminate\Support\Facades\Route;
use Modules\Cms\Http\Controllers\CmsController;

Route::prefix('v1')->group(function () {
    Route::get('cms-contents', [CmsController::class, 'contents']);
    Route::get('banners', [CmsController::class, 'banners']);
});
