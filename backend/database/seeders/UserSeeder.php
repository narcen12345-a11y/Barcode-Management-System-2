<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'email' => 'admin@barcode-system.com',
                'password' => Hash::make('admin123'),
                'full_name' => 'Super Administrator',
                'status' => 'active',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->roles()->syncWithoutDetaching(
            Role::where('name', 'super_admin')->pluck('id')
        );

        // Admin
        $admin = User::firstOrCreate(
            ['username' => 'manager'],
            [
                'email' => 'manager@barcode-system.com',
                'password' => Hash::make('admin123'),
                'full_name' => 'System Manager',
                'status' => 'active',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->roles()->syncWithoutDetaching(
            Role::where('name', 'admin')->pluck('id')
        );

        // Regular User
        $user = User::firstOrCreate(
            ['username' => 'operator'],
            [
                'email' => 'operator@barcode-system.com',
                'password' => Hash::make('admin123'),
                'full_name' => 'System Operator',
                'status' => 'active',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $user->roles()->syncWithoutDetaching(
            Role::where('name', 'user')->pluck('id')
        );
    }
}
