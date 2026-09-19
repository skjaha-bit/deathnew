<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage-users',
            'manage-roles',
            'manage-reference-data',
            'manage-content',
            'manage-subscriptions',
            'manage-settings',
            'view-reports',
            'view-activity-logs',
            'send-notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::query()->firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions);

        $support = Role::query()->firstOrCreate(['name' => 'support-agent', 'guard_name' => 'web']);
        $support->syncPermissions(['manage-users', 'view-reports', 'view-activity-logs']);
    }
}
