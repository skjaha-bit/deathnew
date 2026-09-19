<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $term = '%'.$request->query('search').'%';
                $q->where('name', 'like', $term)->orWhere('email', 'like', $term);
            }))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->withCount('relatives')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin::users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('roles');
        $roles = Role::query()->where('guard_name', 'web')->get();

        return view('admin::users.show', compact('user', 'roles'));
    }

    public function updateStatus(Request $request, User $user)
    {
        $data = $request->validate([
            'status' => ['required', 'in:active,suspended,deceased'],
        ]);

        $user->update($data);

        return back()->with('status', 'User status updated.');
    }

    public function syncRoles(Request $request, User $user)
    {
        $data = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $user->syncRoles($data['roles'] ?? []);

        return back()->with('status', 'Roles updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'User deleted.');
    }
}
