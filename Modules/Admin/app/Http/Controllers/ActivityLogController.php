<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = Activity::query()
            ->with('causer', 'subject')
            ->when($request->filled('log_name'), fn ($q) => $q->where('log_name', $request->query('log_name')))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $logNames = Activity::query()->distinct()->pluck('log_name')->filter();

        return view('admin::activity-logs.index', compact('logs', 'logNames'));
    }
}
