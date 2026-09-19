<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controller;
use Modules\Relatives\Models\Relative;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Models\SubscriptionPlan;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::query()->count(),
            'active_users' => User::query()->where('status', 'active')->count(),
            'pending_relative_invites' => Relative::query()->where('invite_status', 'pending')->count(),
            'active_subscriptions' => Subscription::query()->where('status', 'active')->count(),
        ];

        $signupsByDay = User::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $planBreakdown = SubscriptionPlan::query()
            ->withCount('subscriptions')
            ->get(['id', 'name'])
            ->pluck('subscriptions_count', 'name');

        return view('admin::dashboard.index', compact('stats', 'signupsByDay', 'planBreakdown'));
    }
}
