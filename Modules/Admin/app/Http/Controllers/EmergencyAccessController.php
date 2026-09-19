<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\EmergencyAccess\Models\EmergencyAccessRequest;
use Modules\EmergencyAccess\Services\VaultAccessService;
use Modules\Notification\Notifications\EmergencyAccessDecided;
use Modules\Notification\Notifications\EmergencyAccessRequested;
use Spatie\Permission\Models\Role;

class EmergencyAccessController extends Controller
{
    public const ACCESS_WINDOW_HOURS = 72;

    public function index(Request $request, VaultAccessService $vaultAccessService)
    {
        $actor = Auth::guard('web')->user();

        $requests = EmergencyAccessRequest::query()
            ->with(['targetUser', 'requester', 'approver'])
            ->latest('requested_at')
            ->paginate(20)
            ->withQueryString();

        $requestUser = null;
        $myActiveGrant = null;

        if ($request->filled('user')) {
            $requestUser = User::query()->find($request->query('user'));

            if ($requestUser) {
                $myActiveGrant = $vaultAccessService->findActiveGrant($requestUser, $actor);
            }
        }

        return view('admin::emergency-access.index', compact('requests', 'requestUser', 'myActiveGrant'));
    }

    public function store(Request $request, User $user)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        abort_unless($user->status === User::STATUS_DECEASED, 422, 'Emergency access can only be requested for a deceased user.');

        $actor = Auth::guard('web')->user();

        $existingPending = EmergencyAccessRequest::query()
            ->where('target_user_id', $user->id)
            ->where('requested_by', $actor->id)
            ->where('status', EmergencyAccessRequest::STATUS_PENDING)
            ->exists();

        if ($existingPending) {
            return back()->with('error', 'You already have a pending request for this user.');
        }

        $emergencyAccessRequest = EmergencyAccessRequest::query()->create([
            'target_user_id' => $user->id,
            'requested_by' => $actor->id,
            'status' => EmergencyAccessRequest::STATUS_PENDING,
            'reason' => $data['reason'],
            'requested_at' => now(),
            'request_ip' => $request->ip(),
            'request_user_agent' => (string) $request->userAgent(),
        ]);

        activity()->causedBy($actor)->performedOn($emergencyAccessRequest)
            ->withProperties(['target_user_id' => $user->id, 'reason' => $data['reason']])
            ->log('emergency-access.requested');

        $approverIds = Role::query()->where('name', 'super-admin')->first()?->users->pluck('id') ?? collect();
        $approvers = User::query()->whereIn('id', $approverIds)->where('id', '!=', $actor->id)->get();

        foreach ($approvers as $approver) {
            $approver->notify(new EmergencyAccessRequested($emergencyAccessRequest));
        }

        return redirect()->route('admin.emergency-access.index')->with('status', 'Emergency access requested. A different admin must approve it.');
    }

    public function approve(Request $request, EmergencyAccessRequest $emergencyAccessRequest)
    {
        $data = $request->validate([
            'decision_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $actor = Auth::guard('web')->user();

        abort_unless($emergencyAccessRequest->status === EmergencyAccessRequest::STATUS_PENDING, 422, 'This request is no longer pending.');

        // Defense in depth: the route already requires approve-emergency-access
        // (super-admin only today), but re-check the requester/approver are
        // different people regardless of how permissions evolve later.
        abort_if($actor->id === $emergencyAccessRequest->requested_by, 403, 'You cannot approve your own request.');

        $emergencyAccessRequest->update([
            'status' => EmergencyAccessRequest::STATUS_APPROVED,
            'approved_by' => $actor->id,
            'decision_reason' => $data['decision_reason'] ?? null,
            'decided_at' => now(),
            'access_expires_at' => now()->addHours(self::ACCESS_WINDOW_HOURS),
            'decision_ip' => $request->ip(),
            'decision_user_agent' => (string) $request->userAgent(),
        ]);

        activity()->causedBy($actor)->performedOn($emergencyAccessRequest)->log('emergency-access.approved');

        $emergencyAccessRequest->requester->notify(new EmergencyAccessDecided($emergencyAccessRequest));

        return back()->with('status', 'Access approved for '.self::ACCESS_WINDOW_HOURS.' hours.');
    }

    public function deny(Request $request, EmergencyAccessRequest $emergencyAccessRequest)
    {
        $data = $request->validate([
            'decision_reason' => ['required', 'string', 'max:1000'],
        ]);

        $actor = Auth::guard('web')->user();

        abort_unless($emergencyAccessRequest->status === EmergencyAccessRequest::STATUS_PENDING, 422, 'This request is no longer pending.');
        abort_if($actor->id === $emergencyAccessRequest->requested_by, 403, 'You cannot decide your own request.');

        $emergencyAccessRequest->update([
            'status' => EmergencyAccessRequest::STATUS_DENIED,
            'approved_by' => $actor->id,
            'decision_reason' => $data['decision_reason'],
            'decided_at' => now(),
            'decision_ip' => $request->ip(),
            'decision_user_agent' => (string) $request->userAgent(),
        ]);

        activity()->causedBy($actor)->performedOn($emergencyAccessRequest)->log('emergency-access.denied');

        $emergencyAccessRequest->requester->notify(new EmergencyAccessDecided($emergencyAccessRequest));

        return back()->with('status', 'Request denied.');
    }

    public function revoke(EmergencyAccessRequest $emergencyAccessRequest)
    {
        $actor = Auth::guard('web')->user();

        abort_unless($emergencyAccessRequest->status === EmergencyAccessRequest::STATUS_APPROVED, 422, 'Only an approved grant can be revoked.');

        $emergencyAccessRequest->update([
            'status' => EmergencyAccessRequest::STATUS_REVOKED,
            'revoked_at' => now(),
            'revoked_by' => $actor->id,
        ]);

        activity()->causedBy($actor)->performedOn($emergencyAccessRequest)->log('emergency-access.revoked');

        return back()->with('status', 'Access revoked.');
    }
}
