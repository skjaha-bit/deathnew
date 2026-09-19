<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\ActivityLogController;
use Modules\Admin\Http\Controllers\Auth\LoginController;
use Modules\Admin\Http\Controllers\Auth\PasswordResetController;
use Modules\Admin\Http\Controllers\AdminUserController;
use Modules\Admin\Http\Controllers\DashboardController;
use Modules\Admin\Http\Controllers\NotificationBroadcastController;
use Modules\Admin\Http\Controllers\RelativeController;
use Modules\Admin\Http\Controllers\ReportController;
use Modules\Admin\Http\Controllers\RoleController;
use Modules\Admin\Http\Controllers\UserController;

Route::prefix('admin')->name('admin.')->middleware('web')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.attempt');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [PasswordResetController::class, 'reset'])->name('password.update');

    Route::middleware(['auth', 'role:admin|super-admin|support-agent'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::middleware('permission:manage-users')->group(function () {
            Route::get('users', [UserController::class, 'index'])->name('users.index');
            Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
            Route::put('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update-status');
            Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

            Route::get('relatives', [RelativeController::class, 'index'])->name('relatives.index');
            Route::get('relatives/{relative}', [RelativeController::class, 'show'])->name('relatives.show');
            Route::delete('relatives/{relative}', [RelativeController::class, 'destroy'])->name('relatives.destroy');
        });

        // Admin-tier role assignment is a distinct, higher-privilege action than
        // regular customer management, so it gets its own permission gate rather
        // than riding along with manage-users (which support-agent also holds).
        Route::middleware('permission:manage-admins')->group(function () {
            Route::put('users/{user}/roles', [UserController::class, 'syncRoles'])->name('users.sync-roles');

            Route::get('admin-users', [AdminUserController::class, 'index'])->name('admin-users.index');
            Route::get('admin-users/create', [AdminUserController::class, 'create'])->name('admin-users.create');
            Route::post('admin-users', [AdminUserController::class, 'store'])->name('admin-users.store');
            Route::delete('admin-users/{user}', [AdminUserController::class, 'destroy'])->name('admin-users.destroy');
        });

        Route::middleware('permission:manage-roles')->group(function () {
            Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
            Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
            Route::put('roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.sync-permissions');
            Route::post('permissions', [RoleController::class, 'storePermission'])->name('permissions.store');
            Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        });

        Route::middleware('permission:view-activity-logs')->group(function () {
            Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        });

        Route::middleware('permission:view-reports')->group(function () {
            Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('reports/users/export', [ReportController::class, 'exportUsers'])->name('reports.export-users');
        });

        Route::middleware('permission:send-notifications')->group(function () {
            Route::get('notifications', [NotificationBroadcastController::class, 'index'])->name('notifications.index');
            Route::post('notifications', [NotificationBroadcastController::class, 'store'])->name('notifications.store');
        });
    });
});
