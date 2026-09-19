<?php

use Illuminate\Support\Facades\Route;
use Modules\Cms\Http\Controllers\Admin\BannerController;
use Modules\Cms\Http\Controllers\Admin\CmsContentController;

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'role:admin|super-admin|support-agent', 'permission:manage-content'])->group(function () {
    $lookups = [
        'banners' => BannerController::class,
        'cms-contents' => CmsContentController::class,
    ];

    foreach ($lookups as $uri => $controller) {
        Route::name($uri.'.')->prefix($uri)->group(function () use ($controller) {
            Route::get('/', [$controller, 'index'])->name('index');
            Route::post('/', [$controller, 'store'])->name('store');
            Route::put('{id}', [$controller, 'update'])->name('update');
            Route::delete('{id}', [$controller, 'destroy'])->name('destroy');
            Route::post('{id}/toggle-active', [$controller, 'toggleActive'])->name('toggle-active');
        });
    }
});
