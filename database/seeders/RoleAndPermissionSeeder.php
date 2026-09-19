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
            'manage-admins',
            'manage-reference-data',
            'manage-content',
            'manage-subscriptions',
            'manage-settings',
            'view-reports',
            'view-activity-logs',
            'send-notifications',
            'mark-user-deceased',
            'request-emergency-access',
            'approve-emergency-access',
            'view-emergency-access-audit',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::query()->firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($permissions);

        $admin = Role::query()->firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'manage-users',
            'manage-reference-data',
            'manage-content',
            'manage-subscriptions',
            'manage-settings',
            'view-reports',
            'view-activity-logs',
            'send-notifications',
            'mark-user-deceased',
            'request-emergency-access',
        ]);

        $support = Role::query()->firstOrCreate(['name' => 'support-agent', 'guard_name' => 'web']);
        $support->syncPermissions(['manage-users', 'view-reports', 'view-activity-logs']);
    }
}
