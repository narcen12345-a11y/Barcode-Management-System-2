<?php

namespace App\Enums;

enum PermissionEnum: string
{
    // User Management
    case CREATE_USER = 'create-user';
    case READ_USER = 'read-user';
    case UPDATE_USER = 'update-user';
    case DELETE_USER = 'delete-user';
    case VERIFY_USER = 'verify-user';
    case ACTIVATE_USER = 'activate-user';
    case DEACTIVATE_USER = 'deactivate-user';
    case RESET_PASSWORD = 'reset-password';

    // Role Management
    case CREATE_ROLE = 'create-role';
    case READ_ROLE = 'read-role';
    case UPDATE_ROLE = 'update-role';
    case DELETE_ROLE = 'delete-role';

    // Permission Management
    case CREATE_PERMISSION = 'create-permission';
    case READ_PERMISSION = 'read-permission';
    case UPDATE_PERMISSION = 'update-permission';
    case DELETE_PERMISSION = 'delete-permission';

    // Barcode
    case CREATE_BARCODE = 'create-barcode';
    case READ_BARCODE = 'read-barcode';
    case UPDATE_BARCODE = 'update-barcode';
    case DELETE_BARCODE = 'delete-barcode';
    case RESTORE_BARCODE = 'restore-barcode';

    // Master Data
    case CREATE_SITE = 'create-site';
    case READ_SITE = 'read-site';
    case UPDATE_SITE = 'update-site';
    case DELETE_SITE = 'delete-site';
    case CREATE_MATERIAL = 'create-material';
    case READ_MATERIAL = 'read-material';
    case UPDATE_MATERIAL = 'update-material';
    case DELETE_MATERIAL = 'delete-material';
    case CREATE_TYPE = 'create-type';
    case READ_TYPE = 'read-type';
    case UPDATE_TYPE = 'update-type';
    case DELETE_TYPE = 'delete-type';
    case CREATE_MODEL = 'create-model';
    case READ_MODEL = 'read-model';
    case UPDATE_MODEL = 'update-model';
    case DELETE_MODEL = 'delete-model';

    // Audit
    case READ_AUDIT_LOG = 'read-audit-log';

    // Spreadsheet
    case EXPORT_SPREADSHEET = 'export-spreadsheet';

    public function label(): string
    {
        return match ($this) {
            self::CREATE_USER => 'Create User',
            self::READ_USER => 'Read User',
            self::UPDATE_USER => 'Update User',
            self::DELETE_USER => 'Delete User',
            self::VERIFY_USER => 'Verify User',
            self::ACTIVATE_USER => 'Activate User',
            self::DEACTIVATE_USER => 'Deactivate User',
            self::RESET_PASSWORD => 'Reset Password',
            self::CREATE_ROLE => 'Create Role',
            self::READ_ROLE => 'Read Role',
            self::UPDATE_ROLE => 'Update Role',
            self::DELETE_ROLE => 'Delete Role',
            self::CREATE_PERMISSION => 'Create Permission',
            self::READ_PERMISSION => 'Read Permission',
            self::UPDATE_PERMISSION => 'Update Permission',
            self::DELETE_PERMISSION => 'Delete Permission',
            self::CREATE_BARCODE => 'Create Barcode',
            self::READ_BARCODE => 'Read Barcode',
            self::UPDATE_BARCODE => 'Update Barcode',
            self::DELETE_BARCODE => 'Delete Barcode',
            self::RESTORE_BARCODE => 'Restore Barcode',
            self::CREATE_SITE => 'Create Site',
            self::READ_SITE => 'Read Site',
            self::UPDATE_SITE => 'Update Site',
            self::DELETE_SITE => 'Delete Site',
            self::CREATE_MATERIAL => 'Create Material',
            self::READ_MATERIAL => 'Read Material',
            self::UPDATE_MATERIAL => 'Update Material',
            self::DELETE_MATERIAL => 'Delete Material',
            self::CREATE_TYPE => 'Create Type',
            self::READ_TYPE => 'Read Type',
            self::UPDATE_TYPE => 'Update Type',
            self::DELETE_TYPE => 'Delete Type',
            self::CREATE_MODEL => 'Create Model',
            self::READ_MODEL => 'Read Model',
            self::UPDATE_MODEL => 'Update Model',
            self::DELETE_MODEL => 'Delete Model',
            self::READ_AUDIT_LOG => 'Read Audit Log',
            self::EXPORT_SPREADSHEET => 'Export Spreadsheet',
        };
    }

    public function module(): string
    {
        return match ($this) {
            self::CREATE_USER, self::READ_USER, self::UPDATE_USER,
            self::DELETE_USER, self::VERIFY_USER, self::ACTIVATE_USER,
            self::DEACTIVATE_USER, self::RESET_PASSWORD => 'User Management',
            self::CREATE_ROLE, self::READ_ROLE, self::UPDATE_ROLE,
            self::DELETE_ROLE => 'Role Management',
            self::CREATE_PERMISSION, self::READ_PERMISSION,
            self::UPDATE_PERMISSION, self::DELETE_PERMISSION => 'Permission Management',
            self::CREATE_BARCODE, self::READ_BARCODE, self::UPDATE_BARCODE,
            self::DELETE_BARCODE, self::RESTORE_BARCODE => 'Barcode',
            self::CREATE_SITE, self::READ_SITE, self::UPDATE_SITE,
            self::DELETE_SITE => 'Site',
            self::CREATE_MATERIAL, self::READ_MATERIAL, self::UPDATE_MATERIAL,
            self::DELETE_MATERIAL => 'Material',
            self::CREATE_TYPE, self::READ_TYPE, self::UPDATE_TYPE,
            self::DELETE_TYPE => 'Type',
            self::CREATE_MODEL, self::READ_MODEL, self::UPDATE_MODEL,
            self::DELETE_MODEL => 'Model',
            self::READ_AUDIT_LOG => 'Audit',
            self::EXPORT_SPREADSHEET => 'Spreadsheet',
        };
    }
}
