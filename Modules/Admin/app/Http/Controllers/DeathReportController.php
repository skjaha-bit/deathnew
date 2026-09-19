<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Relatives\Models\DeathReport;

class DeathReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = DeathReport::query()
            ->with('user', 'relative')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')), fn ($q) => $q->where('status', DeathReport::STATUS_PENDING))
            ->latest('reported_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin::death-reports.index', compact('reports'));
    }

    public function confirm(Request $request, DeathReport $deathReport)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $actor = Auth::guard('web')->user();
        abort_unless($actor->can('mark-user-deceased'), 403);

        $user = $deathReport->user;

        if ($user->status !== User::STATUS_DECEASED) {
            $user->forceFill([
                'status' => User::STATUS_DECEASED,
                'deceased_at' => now(),
                'deceased_confirmed_by' => $actor->id,
                'deceased_source' => 'relative_report',
            ])->save();

            activity()->causedBy($actor)->performedOn($user)
                ->withProperties(['reason' => $data['reason'], 'death_report_id' => $deathReport->id])
                ->log('user.marked_deceased');
        }

        $deathReport->update([
            'status' => DeathReport::STATUS_CONFIRMED,
            'reviewed_by' => $actor->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('status', 'Death report confirmed and account marked deceased.');
    }

    public function reject(Request $request, DeathReport $deathReport)
    {
        $actor = Auth::guard('web')->user();

        $deathReport->update([
            'status' => DeathReport::STATUS_REJECTED,
            'reviewed_by' => $actor->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('status', 'Death report rejected.');
    }
}
