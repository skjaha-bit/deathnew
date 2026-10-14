<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->whereEmail('admin@fyrirhyggja.is')->firstOrNew()
            ->fill([
                'email' => 'admin@fyrirhyggja.is',
                'name' => 'FYRIRHYGGJA Admin',
                'password' => Hash::make('Password123!'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        $admin->save();

        $admin->syncRoles(['admin']);
    }
}
