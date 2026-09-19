<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Overview\Http\Services\OverviewService;

class UserController extends Controller
{
    protected const ADMIN_TIER_ROLES = ['super-admin', 'admin', 'support-agent'];

    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $term = '%'.$request->query('search').'%';
                $q->where('name', 'like', $term)->orWhere('email', 'like', $term);
            }))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->withCount('relatives')
            ->with('subscription.plan')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin::users.index', compact('users'));
    }

    public function show(User $user, OverviewService $overviewService)
    {
        $user->load(['roles', 'relatives', 'subscription.plan', 'userSubscriptions.subscriptionType']);

        $overview = $overviewService->getDashboard($user->id);

        return view('admin::users.show', compact('user', 'overview'));
    }

    public function updateStatus(Request $request, User $user)
    {
        $data = $request->validate([
            'status' => ['required', 'in:active,suspended,deceased'],
        ]);

        $user->update($data);

        return back()->with('status', 'User status updated.');
    }

    /**
     * Assign admin-tier roles (super-admin/admin/support-agent) to a user.
     * This is the only place admin-tier role membership changes, so it is
     * gated by manage-admins (not the broader manage-users) and guards
     * against a non-super-admin granting/revoking the super-admin role.
     */
    public function syncRoles(Request $request, User $user)
    {
        $data = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $requestedRoles = $data['roles'] ?? [];
        $actor = Auth::guard('web')->user();

        $grantsOrKeepsSuperAdmin = in_array('super-admin', $requestedRoles, true);
        $currentlySuperAdmin = $user->hasRole('super-admin');

        if (($grantsOrKeepsSuperAdmin || $currentlySuperAdmin) && ! $actor->hasRole('super-admin')) {
            abort(403, 'Only a super-admin can grant or revoke the super-admin role.');
        }

        if ($currentlySuperAdmin && ! $grantsOrKeepsSuperAdmin) {
            $remainingSuperAdmins = User::query()->role('super-admin')->where('id', '!=', $user->id)->count();

            if ($remainingSuperAdmins === 0) {
                return back()->with('error', 'Cannot remove the last remaining super-admin.');
            }
        }

        $user->syncRoles($requestedRoles);

        return back()->with('status', 'Roles updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'User deleted.');
    }
}
