<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Modules\Core\Support\ValidationPatterns;

class AdminUserController extends Controller
{
    protected const ADMIN_TIER_ROLES = ['super-admin', 'admin', 'support-agent'];

    public function index(Request $request)
    {
        $users = User::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('name', self::ADMIN_TIER_ROLES))
            ->with('roles')
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $term = '%'.$request->query('search').'%';
                $q->where('name', 'like', $term)->orWhere('email', 'like', $term);
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin::admin-users.index', compact('users'));
    }

    public function create()
    {
        $actor = Auth::guard('web')->user();
        $assignableRoles = $actor->hasRole('super-admin') ? self::ADMIN_TIER_ROLES : ['admin', 'support-agent'];

        return view('admin::admin-users.create', compact('assignableRoles'));
    }

    public function store(Request $request)
    {
        $actor = Auth::guard('web')->user();
        $assignableRoles = $actor->hasRole('super-admin') ? self::ADMIN_TIER_ROLES : ['admin', 'support-agent'];

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', 'in:'.implode(',', $assignableRoles)],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(40)),
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        $user->assignRole($data['role']);

        Password::sendResetLink(['email' => $user->email]);

        activity()
            ->causedBy($actor)
            ->performedOn($user)
            ->withProperties(['role' => $data['role']])
            ->log('admin_user.created');

        return redirect()->route('admin.admin-users.index')
            ->with('status', "Admin user created. A password-set link was sent to {$user->email}.");
    }

    public function destroy(Request $request, User $user)
    {
        $actor = Auth::guard('web')->user();

        if ($actor->id === $user->id) {
            return back()->with('error', 'You cannot remove your own admin access.');
        }

        if ($user->hasRole('super-admin')) {
            $remainingSuperAdmins = User::query()->role('super-admin')->where('id', '!=', $user->id)->count();

            if ($remainingSuperAdmins === 0) {
                return back()->with('error', 'Cannot remove the last remaining super-admin.');
            }
        }

        $user->syncRoles([]);

        activity()->causedBy($actor)->performedOn($user)->log('admin_user.access_revoked');

        return redirect()->route('admin.admin-users.index')->with('status', 'Admin access revoked for '.$user->name.'.');
    }
}
