<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::query()->where('guard_name', 'web')->with('permissions')->get();
        $permissions = Permission::query()->where('guard_name', 'web')->orderBy('name')->get();

        return view('admin::roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_\-\.]+$/i', 'unique:roles,name'],
        ], [
            'name.regex' => 'The role name may only contain letters, digits, underscores, hyphens and periods.',
        ]);

        Role::query()->create(['name' => $data['name'], 'guard_name' => 'web']);

        return back()->with('status', 'Role created.');
    }

    public function syncPermissions(Request $request, Role $role)
    {
        $data = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return back()->with('status', 'Permissions updated for '.$role->name.'.');
    }

    public function storePermission(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_\-\.]+$/i', 'unique:permissions,name'],
        ], [
            'name.regex' => 'The permission name may only contain letters, digits, underscores, hyphens and periods.',
        ]);

        Permission::query()->create(['name' => $data['name'], 'guard_name' => 'web']);

        return back()->with('status', 'Permission created.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['admin', 'super-admin', 'support-agent'], true)) {
            return back()->with('error', 'Cannot delete a built-in admin-tier role.');
        }

        $role->delete();

        return back()->with('status', 'Role deleted.');
    }
}
