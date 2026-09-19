<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\Http\Controllers\NotificationController;

Route::prefix('v1/notifications')->middleware('auth:api')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead']);
});
