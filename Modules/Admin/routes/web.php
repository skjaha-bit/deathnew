<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\ActivityLogController;
use Modules\Admin\Http\Controllers\Auth\LoginController;
use Modules\Admin\Http\Controllers\DashboardController;
use Modules\Admin\Http\Controllers\NotificationBroadcastController;
use Modules\Admin\Http\Controllers\ReportController;
use Modules\Admin\Http\Controllers\RoleController;
use Modules\Admin\Http\Controllers\UserController;

Route::prefix('admin')->name('admin.')->middleware('web')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.attempt');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::put('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update-status');
        Route::put('users/{user}/roles', [UserController::class, 'syncRoles'])->name('users.sync-roles');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.sync-permissions');
        Route::post('permissions', [RoleController::class, 'storePermission'])->name('permissions.store');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/users/export', [ReportController::class, 'exportUsers'])->name('reports.export-users');

        Route::get('notifications', [NotificationBroadcastController::class, 'index'])->name('notifications.index');
        Route::post('notifications', [NotificationBroadcastController::class, 'store'])->name('notifications.store');
    });
});
