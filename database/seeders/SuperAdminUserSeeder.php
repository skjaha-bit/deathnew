<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::query()->whereEmail('superadmin@fyrirhyggja.is')->firstOrNew()
            ->fill([
                'email' => 'superadmin@fyrirhyggja.is',
                'name' => 'FYRIRHYGGJA Super Admin',
                'password' => Hash::make('Password123!'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        $superAdmin->save();

        $superAdmin->syncRoles(['super-admin']);
    }
}
