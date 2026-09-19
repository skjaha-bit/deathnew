<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Modules\Core\Support\ValidationPatterns;
use Modules\Notification\Notifications\AdminBroadcastNotification;

class NotificationBroadcastController extends Controller
{
    public function index()
    {
        $recent = \Illuminate\Notifications\DatabaseNotification::query()
            ->where('type', AdminBroadcastNotification::class)
            ->latest()
            ->limit(20)
            ->get();

        return view('admin::notifications.index', compact('recent'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'message' => ['required', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'audience' => ['required', 'in:all,active'],
        ], [
            'title.regex' => 'The title may not contain < or > characters.',
            'message.regex' => 'The message may not contain < or > characters.',
        ]);

        $users = User::query()
            ->when($data['audience'] === 'active', fn ($q) => $q->where('status', 'active'))
            ->get();

        NotificationFacade::send($users, new AdminBroadcastNotification($data['title'], $data['message']));

        return back()->with('status', "Notification sent to {$users->count()} user(s).");
    }
}
