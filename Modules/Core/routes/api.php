<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\CoreController;

Route::prefix('v1')->group(function () {
    Route::get('languages', [CoreController::class, 'languages']);
    Route::get('countries', [CoreController::class, 'countries']);
    Route::get('translations/all', [CoreController::class, 'allTranslations']);
    Route::get('translations', [CoreController::class, 'translations']);
});
