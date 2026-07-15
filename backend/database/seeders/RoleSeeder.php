<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin — gets all permissions
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super_admin'],
            [
                'display_name' => 'Super Admin',
                'description' => 'Full access to all features and settings.',
                'is_active' => true,
            ]
        );
        $superAdmin->syncPermissions(Permission::pluck('id')->toArray());

        // Admin — gets most permissions except user verification/activation
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Admin',
                'description' => 'Administrative access to manage master data and barcodes.',
                'is_active' => true,
            ]
        );
        $adminPermissions = Permission::whereNotIn('name', [
            'verify-user',
            'activate-user',
            'deactivate-user',
            'reset-password',
            'create-role',
            'update-role',
            'delete-role',
            'create-permission',
            'update-permission',
            'delete-permission',
        ])->pluck('id')->toArray();
        $admin->syncPermissions($adminPermissions);

        // User — read-only + create/update barcodes
        $user = Role::firstOrCreate(
            ['name' => 'user'],
            [
                'display_name' => 'User',
                'description' => 'Basic user with read access and barcode management.',
                'is_active' => true,
            ]
        );
        $userPermissions = Permission::whereIn('name', [
            'read-site',
            'read-material-type',
            'read-material-model',
            'read-material',
            'read-barcode',
            'create-barcode',
            'update-barcode',
        ])->pluck('id')->toArray();
        $user->syncPermissions($userPermissions);
    }
}
