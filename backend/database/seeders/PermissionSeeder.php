<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * All permissions used by the application.
     * Format: [name, display_name, module]
     */
    private const PERMISSIONS = [
        // User Management
        ['create-user', 'Create User', 'User Management'],
        ['read-user', 'Read User', 'User Management'],
        ['update-user', 'Update User', 'User Management'],
        ['delete-user', 'Delete User', 'User Management'],
        ['verify-user', 'Verify User', 'User Management'],
        ['activate-user', 'Activate User', 'User Management'],
        ['deactivate-user', 'Deactivate User', 'User Management'],
        ['reset-password', 'Reset Password', 'User Management'],

        // Role Management
        ['create-role', 'Create Role', 'Role Management'],
        ['read-role', 'Read Role', 'Role Management'],
        ['update-role', 'Update Role', 'Role Management'],
        ['delete-role', 'Delete Role', 'Role Management'],

        // Permission Management
        ['create-permission', 'Create Permission', 'Permission Management'],
        ['read-permission', 'Read Permission', 'Permission Management'],
        ['update-permission', 'Update Permission', 'Permission Management'],
        ['delete-permission', 'Delete Permission', 'Permission Management'],

        // Site Management
        ['create-site', 'Create Site', 'Site Management'],
        ['read-site', 'Read Site', 'Site Management'],
        ['update-site', 'Update Site', 'Site Management'],
        ['delete-site', 'Delete Site', 'Site Management'],

        // Material Type Management
        ['create-material-type', 'Create Material Type', 'Material Type Management'],
        ['read-material-type', 'Read Material Type', 'Material Type Management'],
        ['update-material-type', 'Update Material Type', 'Material Type Management'],
        ['delete-material-type', 'Delete Material Type', 'Material Type Management'],

        // Material Model Management
        ['create-material-model', 'Create Material Model', 'Material Model Management'],
        ['read-material-model', 'Read Material Model', 'Material Model Management'],
        ['update-material-model', 'Update Material Model', 'Material Model Management'],
        ['delete-material-model', 'Delete Material Model', 'Material Model Management'],

        // Material Management
        ['create-material', 'Create Material', 'Material Management'],
        ['read-material', 'Read Material', 'Material Management'],
        ['update-material', 'Update Material', 'Material Management'],
        ['delete-material', 'Delete Material', 'Material Management'],

        // Barcode Management
        ['create-barcode', 'Create Barcode', 'Barcode Management'],
        ['read-barcode', 'Read Barcode', 'Barcode Management'],
        ['update-barcode', 'Update Barcode', 'Barcode Management'],
        ['delete-barcode', 'Delete Barcode', 'Barcode Management'],
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as [$name, $displayName, $module]) {
            Permission::firstOrCreate(
                ['name' => $name],
                [
                    'display_name' => $displayName,
                    'module' => $module,
                    'description' => "Allows user to {$displayName}.",
                    'is_active' => true,
                ]
            );
        }
    }
}
