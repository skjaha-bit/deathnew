<?php

namespace Modules\Notification\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponse;
use Modules\Notification\Http\Services\NotificationService;

class NotificationController extends Controller
{
    use ApiResponse;

    public function __construct(protected NotificationService $notificationService) {}

    /**
     * List the authenticated user's notifications, paginated.
     */
    public function index(Request $request)
    {
        $paginator = $this->notificationService->listForUser($request->user());

        return $this->success([
            'items' => array_map(fn ($n) => $this->formatNotification($n), $paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * Get the count of unread notifications for the authenticated user.
     */
    public function unreadCount(Request $request)
    {
        return $this->success([
            'count' => $this->notificationService->unreadCountForUser($request->user()),
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, string $id)
    {
        $this->notificationService->markAsRead($request->user(), $id);

        return $this->success(null, 'Notification marked as read.');
    }

    /**
     * Mark all of the authenticated user's unread notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $this->notificationService->markAllAsReadForUser($request->user());

        return $this->success(null, 'All notifications marked as read.');
    }

    private function formatNotification(Model $n): array
    {
        return [
            'id' => $n->id,
            'type' => class_basename($n->type),
            'data' => $n->data,
            'read_at' => $n->read_at?->toIso8601String(),
            'created_at' => $n->created_at?->toIso8601String(),
        ];
    }
}
