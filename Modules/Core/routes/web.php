<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Admin\LanguageController;
use Modules\Core\Http\Controllers\Admin\SettingController;
use Modules\Core\Http\Controllers\Admin\TranslationController;

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'role:admin|super-admin|support-agent', 'permission:manage-settings'])->group(function () {
    Route::get('languages', [LanguageController::class, 'index'])->name('languages.index');
    Route::post('languages', [LanguageController::class, 'store'])->name('languages.store');
    Route::post('languages/{language}/toggle-active', [LanguageController::class, 'toggleActive'])->name('languages.toggle-active');
    Route::post('languages/{language}/make-default', [LanguageController::class, 'makeDefault'])->name('languages.make-default');
    Route::delete('languages/{language}', [LanguageController::class, 'destroy'])->name('languages.destroy');

    Route::get('translations', [TranslationController::class, 'index'])->name('translations.index');
    Route::post('translations/save-all', [TranslationController::class, 'saveAll'])->name('translations.save-all');
    Route::delete('translations/{translation}', [TranslationController::class, 'destroy'])->name('translations.destroy');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
});
