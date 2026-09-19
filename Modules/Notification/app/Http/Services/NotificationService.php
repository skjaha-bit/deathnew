<?php

namespace Modules\Notification\Http\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotificationService
{
    /**
     * List the given user's notifications, paginated, newest first.
     */
    public function listForUser($user, int $perPage = 20): LengthAwarePaginator
    {
        return $user->notifications()->latest()->paginate($perPage);
    }

    /**
     * Count the given user's unread notifications.
     */
    public function unreadCountForUser($user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Mark a single notification belonging to the given user as read.
     */
    public function markAsRead($user, string $id): void
    {
        $notification = $user->notifications()->findOrFail($id);

        $notification->markAsRead();
    }

    /**
     * Mark all of the given user's unread notifications as read.
     */
    public function markAllAsReadForUser($user): void
    {
        $user->unreadNotifications->markAsRead();
    }
}
