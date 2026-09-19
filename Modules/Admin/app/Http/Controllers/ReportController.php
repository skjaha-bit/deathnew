<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Modules\Subscription\Models\SubscriptionPlan;

class ReportController extends Controller
{
    public function index()
    {
        $usersByStatus = User::query()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $subscriptionsByPlan = SubscriptionPlan::query()->withCount('subscriptions')->get(['id', 'name']);

        return view('admin::reports.index', compact('usersByStatus', 'subscriptionsByPlan'));
    }

    public function exportUsers()
    {
        $users = User::query()->select(['id', 'name', 'email', 'status', 'created_at'])->get();

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Status', 'Registered At']);
            foreach ($users as $user) {
                fputcsv($handle, [$user->id, $user->name, $user->email, $user->status, $user->created_at]);
            }
            fclose($handle);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users-report.csv"',
        ]);
    }
}
