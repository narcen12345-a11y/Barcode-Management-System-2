# Barcode Management System — Project Index

> **Generated:** 2026-07-15  
> **Purpose:** Internal project map to support future audits, onboarding, and architecture reviews.

---

## Table of Contents

1. [Project Overview](#project-overview)
2. [Directory Structure](#directory-structure)
3. [Backend — `backend/`](#backend)
   - [Enums](#enums)
   - [Models](#models)
   - [Interfaces](#interfaces)
   - [Repositories](#repositories)
   - [DTOs](#dtos)
   - [Services](#services)
   - [Controllers](#controllers)
   - [Requests](#requests)
   - [Resources](#resources)
   - [Middleware](#middleware)
   - [Providers](#providers)
   - [Routes](#routes)
4. [Frontend — `frontend/`](#frontend)
5. [Documentation — `docs/`](#documentation)
6. [Tasks — `tasks/`](#tasks)
7. [Infrastructure](#infrastructure)

---

## Project Overview

| Attribute | Value |
|-----------|-------|
| **Name** | Barcode Management System |
| **Stack** | Laravel 11 (Backend API) + React/Vite (Frontend) |
| **Database** | PostgreSQL |
| **Auth** | Laravel Sanctum (Token-based) |
| **Container** | Docker (docker-compose.yml) |
| **Repository** | `https://github.com/narcen12345-a11y/Barcode-Management-System-2.git` |

---

## Directory Structure

```
Barcode Management System/
├── backend/                  # Laravel API
│   ├── app/
│   │   ├── DTOs/             # Data Transfer Objects
│   │   ├── Enums/            # PHP Enums
│   │   ├── Http/
│   │   │   ├── Controllers/  # API Controllers
│   │   │   ├── Middleware/   # HTTP Middleware
│   │   │   ├── Requests/     # Form Requests (Validation)
│   │   │   └── Resources/    # API Resources (Transformers)
│   │   ├── Interfaces/       # Repository Interfaces
│   │   ├── Models/           # Eloquent Models
│   │   ├── Providers/        # Service Providers
│   │   ├── Repositories/     # Repository Implementations
│   │   └── Services/         # Business Logic Services
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/
│   └── routes/
├── docs/                     # Project Documentation
│   ├── project/              # (new) Project index & audit docs
│   └── ... (various .md files)
├── frontend/                 # React + Vite SPA
│   └── src/
├── tasks/                    # Task breakdowns (PHASE-*)
├── docker-compose.yml
├── .gitignore
└── README.md
```

---

## Backend

### Enums

#### `backend/app/Enums/UserStatusEnum.php`

| Case | Value | Description |
|------|-------|-------------|
| `PENDING_VERIFICATION` | `pending_verification` | Awaiting admin verification |
| `ACTIVE` | `active` | Active and can login |
| `INACTIVE` | `inactive` | Deactivated |
| `SUSPENDED` | `suspended` | Suspended |

**Methods:**
- `label(): string` — Human-readable label
- `canLogin(): bool` — Returns `true` only for `ACTIVE`

---

#### `backend/app/Enums/PermissionEnum.php`

Defines all system permissions with `label()` and `module()` methods.

**Permission Cases (29 total):**

| Module | Permissions |
|--------|-------------|
| User Management | `create-user`, `read-user`, `update-user`, `delete-user`, `verify-user`, `activate-user`, `deactivate-user`, `reset-password` |
| Role Management | `create-role`, `read-role`, `update-role`, `delete-role` |
| Permission Management | `create-permission`, `read-permission`, `update-permission`, `delete-permission` |
| Barcode | `create-barcode`, `read-barcode`, `update-barcode`, `delete-barcode`, `restore-barcode` |
| Site | `create-site`, `read-site`, `update-site`, `delete-site` |
| Material | `create-material`, `read-material`, `update-material`, `delete-material` |
| Type | `create-type`, `read-type`, `update-type`, `delete-type` |
| Model | `create-model`, `read-model`, `update-model`, `delete-model` |
| Audit | `read-audit-log` |
| Spreadsheet | `export-spreadsheet` |

---

#### `backend/app/Enums/BarcodeStatusEnum.php`

| Case | Value | Label |
|------|-------|-------|
| `NEW` | `NEW` | NEW (MOS) |
| `OLD` | `OLD` | OLD (DISMANTLE) |

---

#### `backend/app/Enums/BarcodeHistoryTypeEnum.php`

| Case | Value |
|------|-------|
| `CREATE` | `CREATE` |
| `UPDATE` | `UPDATE` |
| `STATUS_CHANGE` | `STATUS_CHANGE` |
| `RESTORE` | `RESTORE` |
| `SOFT_DELETE` | `SOFT_DELETE` |

---

### Models

#### `User` (`backend/app/Models/User.php`)

| Attribute | Details |
|-----------|---------|
| **Extends** | `Authenticatable` |
| **Traits** | `HasApiTokens`, `Notifiable`, `SoftDeletes` |
| **Fillable** | `username`, `email`, `password`, `full_name`, `status`, `is_active`, `email_verified_at`, `last_login_at`, `password_changed_at` |
| **Hidden** | `password`, `remember_token` |
| **Casts** | `is_active` → `boolean`, `email_verified_at` → `datetime`, `last_login_at` → `datetime`, `password_changed_at` → `datetime` |

**Relationships:**
- `roles(): BelongsToMany` — via `role_user` pivot
- `permissions(): BelongsToMany` — via `permission_role` pivot (scoped to user's roles)
- `auditLogs(): HasMany`
- `activityLogs(): HasMany`

**Methods:**
- `hasRole(string $roleName): bool`
- `hasPermission(string $permissionName): bool`
- `hasAnyPermission(array $permissionNames): bool`
- `isSuperAdmin(): bool`
- `isAdmin(): bool`
- `canLogin(): bool`
- `markAsLoggedIn(): void`
- `setPassword(string $password): void`

---

#### `Role` (`backend/app/Models/Role.php`)

| Attribute | Details |
|-----------|---------|
| **Traits** | `SoftDeletes` |
| **Fillable** | `name`, `display_name`, `description`, `is_active` |
| **Casts** | `is_active` → `boolean` |

**Relationships:**
- `users(): BelongsToMany` — via `role_user` pivot
- `permissions(): BelongsToMany` — via `permission_role` pivot

**Methods:**
- `hasPermission(string $permissionName): bool`
- `syncPermissions(array $permissionIds): void`

---

#### `Permission` (`backend/app/Models/Permission.php`)

| Attribute | Details |
|-----------|---------|
| **Traits** | `SoftDeletes` |
| **Fillable** | `name`, `display_name`, `module`, `description`, `is_active` |
| **Casts** | `is_active` → `boolean` |

**Relationships:**
- `roles(): BelongsToMany` — via `permission_role` pivot

---

#### `Site` (`backend/app/Models/Site.php`)

| Attribute | Details |
|-----------|---------|
| **Traits** | `SoftDeletes` |
| **Fillable** | `site_id`, `site_name`, `region`, `address`, `latitude`, `longitude`, `is_active` |
| **Casts** | `is_active` → `boolean` |

**Relationships:**
- `barcodes(): HasMany`

---

#### `MaterialType` (`backend/app/Models/MaterialType.php`)

| Attribute | Details |
|-----------|---------|
| **Traits** | `SoftDeletes` |
| **Fillable** | `name`, `description`, `is_active` |
| **Casts** | `is_active` → `boolean` |

**Relationships:**
- `materials(): HasMany`
- `materialModels(): HasMany`

---

#### `MaterialModel` (`backend/app/Models/MaterialModel.php`)

| Attribute | Details |
|-----------|---------|
| **Traits** | `SoftDeletes` |
| **Fillable** | `material_type_id`, `name`, `description`, `is_active` |
| **Casts** | `is_active` → `boolean` |

**Relationships:**
- `materialType(): BelongsTo`
- `materials(): HasMany`

---

#### `Material` (`backend/app/Models/Material.php`)

| Attribute | Details |
|-----------|---------|
| **Traits** | `SoftDeletes` |
| **Fillable** | `material_type_id`, `material_model_id`, `material_code`, `name`, `description`, `is_active` |
| **Casts** | `is_active` → `boolean` |

**Relationships:**
- `materialType(): BelongsTo`
- `materialModel(): BelongsTo`
- `barcodes(): HasMany`

---

#### `Barcode` (`backend/app/Models/Barcode.php`)

| Attribute | Details |
|-----------|---------|
| **Traits** | `SoftDeletes` |
| **Fillable** | `barcode_id`, `material_id`, `site_id`, `serial_number`, `status`, `description`, `is_active`, `created_by`, `updated_by` |
| **Casts** | `is_active` → `boolean` |

**Relationships:**
- `material(): BelongsTo`
- `site(): BelongsTo`
- `createdBy(): BelongsTo` (User)
- `updatedBy(): BelongsTo` (User)
- `histories(): HasMany`

---

#### `BarcodeHistory` (`backend/app/Models/BarcodeHistory.php`)

| Attribute | Details |
|-----------|---------|
| **Fillable** | `barcode_id`, `field_name`, `old_value`, `new_value`, `changed_by`, `change_reason` |

**Relationships:**
- `barcode(): BelongsTo`
- `changedBy(): BelongsTo` (User)

---

#### `AuditLog` (`backend/app/Models/AuditLog.php`)

| Attribute | Details |
|-----------|---------|
| **Fillable** | `user_id`, `entity_type`, `entity_id`, `action`, `old_values`, `new_values`, `ip_address`, `user_agent` |
| **Casts** | `old_values` → `array`, `new_values` → `array` |
| **Timestamps** | `$timestamps = false`, `UPDATED_AT = null` |

**Relationships:**
- `user(): BelongsTo`

---

#### `ActivityLog` (`backend/app/Models/ActivityLog.php`)

| Attribute | Details |
|-----------|---------|
| **Fillable** | `user_id`, `activity`, `module`, `description`, `ip_address`, `user_agent`, `session_id` |
| **Timestamps** | `$timestamps = false`, `UPDATED_AT = null` |

**Relationships:**
- `user(): BelongsTo`

---

### Interfaces

| Interface | Path | Methods |
|-----------|------|---------|
| `UserRepositoryInterface` | `Interfaces/UserRepositoryInterface.php` | `findById`, `findByUsername`, `findByEmail`, `findByLogin`, `findAllPaginated`, `findAll`, `create`, `update`, `delete`, `restore`, `findOnlyTrashed`, `findOnlyTrashedById`, `countByStatus` |
| `SiteRepositoryInterface` | `Interfaces/SiteRepositoryInterface.php` | `findById`, `findBySiteId`, `findAllPaginated`, `findAll`, `create`, `update`, `delete`, `restore`, `findOnlyTrashed` |
| `MaterialTypeRepositoryInterface` | `Interfaces/MaterialTypeRepositoryInterface.php` | `findById`, `findByName`, `findAllPaginated`, `findAll`, `create`, `update`, `delete`, `restore`, `findOnlyTrashed` |
| `MaterialModelRepositoryInterface` | `Interfaces/MaterialModelRepositoryInterface.php` | `findById`, `findAllPaginated`, `findAll`, `findByMaterialTypeId`, `create`, `update`, `delete`, `restore`, `findOnlyTrashed` |
| `MaterialRepositoryInterface` | `Interfaces/MaterialRepositoryInterface.php` | `findById`, `findByName`, `findAllPaginated`, `findAll`, `create`, `update`, `delete`, `restore`, `findOnlyTrashed` |
| `BarcodeRepositoryInterface` | `Interfaces/BarcodeRepositoryInterface.php` | `findById`, `findByBarcodeId`, `findBySerialNumber`, `findAllPaginated`, `findAll`, `create`, `update`, `delete`, `restore`, `findOnlyTrashed`, `getNextBarcodeSequence` |
| `BarcodeHistoryRepositoryInterface` | `Interfaces/BarcodeHistoryRepositoryInterface.php` | `findById`, `findByBarcodeId`, `findAllPaginatedByBarcode`, `create` |
| `RoleRepositoryInterface` | `Interfaces/RoleRepositoryInterface.php` | `findById`, `findByName`, `findAllPaginated`, `findAll`, `create`, `update`, `delete`, `restore`, `findOnlyTrashed` |
| `PermissionRepositoryInterface` | `Interfaces/PermissionRepositoryInterface.php` | `findById`, `findByName`, `findAllPaginated`, `findAll`, `findByModule`, `create`, `update`, `delete`, `restore`, `findOnlyTrashed` |
| `AuditLogRepositoryInterface` | `Interfaces/AuditLogRepositoryInterface.php` | `create` |
| `ActivityLogRepositoryInterface` | `Interfaces/ActivityLogRepositoryInterface.php` | `create` |

---

### Repositories

#### `UserRepository` (`Repositories/UserRepository.php`)

| Attribute | Details |
|-----------|---------|
| **Implements** | `UserRepositoryInterface` |
| **Model** | `User` |

**Public Methods:** `findById`, `findByUsername`, `findByEmail`, `findByLogin`, `findAllPaginated`, `findAll`, `create`, `update`, `delete`, `restore`, `findOnlyTrashed`, `findOnlyTrashedById`, `countByStatus`

**Filter Support:** `search` (username/email/full_name), `status`, `is_active`, `role`, `trashed` (only/with)

---

#### `SiteRepository` (`Repositories/SiteRepository.php`)

| Attribute | Details |
|-----------|---------|
| **Implements** | `SiteRepositoryInterface` |
| **Model** | `Site` |

**Public Methods:** `findById`, `findBySiteId`, `findAllPaginated`, `findAll`, `create`, `update`, `delete`, `restore`, `findOnlyTrashed`

**Filter Support:** `search` (site_id/site_name), `site_id`, `site_name`, `is_active`, `trashed`

---

#### `MaterialTypeRepository` (`Repositories/MaterialTypeRepository.php`)

| Attribute | Details |
|-----------|---------|
| **Implements** | `MaterialTypeRepositoryInterface` |
| **Model** | `MaterialType` |

**Public Methods:** `findById`, `findByName`, `findAllPaginated`, `findAll`, `create`, `update`, `delete`, `restore`, `findOnlyTrashed`

**Filter Support:** `search` (name), `is_active`, `trashed`

---

#### `MaterialModelRepository` (`Repositories/MaterialModelRepository.php`)

| Attribute | Details |
|-----------|---------|
| **Implements** | `MaterialModelRepositoryInterface` |
| **Model** | `MaterialModel` |

**Public Methods:** `findById`, `findAllPaginated`, `findAll`, `findByMaterialTypeId`, `create`, `update`, `delete`, `restore`, `findOnlyTrashed`

**Filter Support:** `search` (name), `material_type_id`, `is_active`, `trashed`

**Eager Loads:** `materialType`

---

#### `MaterialRepository` (`Repositories/MaterialRepository.php`)

| Attribute | Details |
|-----------|---------|
| **Implements** | `MaterialRepositoryInterface` |
| **Model** | `Material` |

**Public Methods:** `findById`, `findByName`, `findAllPaginated`, `findAll`, `create`, `update`, `delete`, `restore`, `findOnlyTrashed`

**Filter Support:** `search` (name/material_code), `material_type_id`, `material_model_id`, `is_active`, `trashed`

**Eager Loads:** `materialType`, `materialModel`

---

#### `BarcodeRepository` (`Repositories/BarcodeRepository.php`)

| Attribute | Details |
|-----------|---------|
| **Implements** | `BarcodeRepositoryInterface` |
| **Model** | `Barcode` |

**Public Methods:** `findById`, `findByBarcodeId`, `findBySerialNumber`, `findAllPaginated`, `findAll`, `create`, `update`, `delete`, `restore`, `findOnlyTrashed`, `getNextBarcodeSequence`

**Filter Support:** `search` (barcode_id/serial_number), `barcode_id`, `serial_number`, `site_id`, `material_id`, `status`, `is_active`, `date_from`, `date_to`, `trashed`

**Eager Loads:** `material.materialType`, `material.materialModel`, `site`, `createdBy`, `updatedBy`

---

#### `BarcodeHistoryRepository` (`Repositories/BarcodeHistoryRepository.php`)

| Attribute | Details |
|-----------|---------|
| **Implements** | `BarcodeHistoryRepositoryInterface` |
| **Model** | `BarcodeHistory` |

**Public Methods:** `findById`, `findByBarcodeId`, `findAllPaginatedByBarcode`, `create`

**Filter Support:** `field_name`, `changed_by`, `date_from`, `date_to`

**Eager Loads:** `changedBy`

---

#### `RoleRepository` (`Repositories/RoleRepository.php`)

| Attribute | Details |
|-----------|---------|
| **Implements** | `RoleRepositoryInterface` |
| **Model** | `Role` |

**Public Methods:** `findById`, `findByName`, `findAllPaginated`, `findAll`, `create`, `update`, `delete`, `restore`, `findOnlyTrashed`

**Filter Support:** `search` (name/display_name), `is_active`, `trashed`

---

#### `PermissionRepository` (`Repositories/PermissionRepository.php`)

| Attribute | Details |
|-----------|---------|
| **Implements** | `PermissionRepositoryInterface` |
| **Model** | `Permission` |

**Public Methods:** `findById`, `findByName`, `findAllPaginated`, `findAll`, `findByModule`, `create`, `update`, `delete`, `restore`, `findOnlyTrashed`

**Filter Support:** `search` (name/display_name), `module`, `is_active`, `trashed`

---

#### `AuditLogRepository` (`Repositories/AuditLogRepository.php`)

| Attribute | Details |
|-----------|---------|
| **Implements** | `AuditLogRepositoryInterface` |
| **Model** | `AuditLog` |

**Public Methods:** `create`

---

#### `ActivityLogRepository` (`Repositories/ActivityLogRepository.php`)

| Attribute | Details |
|-----------|---------|
| **Implements** | `ActivityLogRepositoryInterface` |
| **Model** | `ActivityLog` |

**Public Methods:** `create`

---

### DTOs

| DTO | Path | Properties | Factory Method |
|-----|------|------------|----------------|
| `LoginRequestDTO` | `DTOs/LoginRequestDTO.php` | `login`, `password`, `remember` | `fromRequest(array $data)` |
| `ChangePasswordDTO` | `DTOs/ChangePasswordDTO.php` | `userId`, `currentPassword`, `newPassword` | `fromRequest(array $data)` |
| `RegisterUserDTO` | `DTOs/RegisterUserDTO.php` | `username`, `email`, `password`, `fullName`, `roleIds` | `fromRequest(array $data)` |
| `VerifyUserDTO` | `DTOs/VerifyUserDTO.php` | `userId`, `status` | `fromRequest(array $data)` |
| `SiteDTO` | `DTOs/SiteDTO.php` | `siteId`, `siteName`, `region`, `address`, `latitude`, `longitude`, `isActive` | `fromRequest(array $data)` + `toArray()` |
| `MaterialTypeDTO` | `DTOs/MaterialTypeDTO.php` | `name`, `description`, `isActive` | `fromRequest(array $data)` + `toArray()` |
| `MaterialModelDTO` | `DTOs/MaterialModelDTO.php` | `materialTypeId`, `name`, `description`, `isActive` | `fromRequest(array $data)` + `toArray()` |
| `MaterialDTO` | `DTOs/MaterialDTO.php` | `materialTypeId`, `materialModelId`, `materialCode`, `name`, `description`, `isActive` | `fromRequest(array $data)` + `toArray()` |
| `BarcodeDTO` | `DTOs/BarcodeDTO.php` | `barcodeId`, `materialId`, `siteId`, `serialNumber`, `status`, `description`, `createdBy` | `fromRequest(array $data, string $barcodeId, ?int $userId)` + `toArray()` |
| `UpdateBarcodeDTO` | `DTOs/UpdateBarcodeDTO.php` | `materialId`, `siteId`, `serialNumber`, `status`, `description`, `updatedBy` | `fromRequest(array $data, ?int $userId)` + `toArray()` |

---

### Services

#### `AuthenticationService` (`Services/AuthenticationService.php`)

| Attribute | Details |
|-----------|---------|
| **Dependencies** | `UserRepositoryInterface`, `AuditLogService`, `ActivityLogService` |
| **DTOs Used** | `LoginRequestDTO`, `ChangePasswordDTO` |

**Business Logic:**
- `login(LoginRequestDTO): array` — Validates credentials, checks `canLogin()`, logs audit/activity, creates Sanctum token
- `logout(User): void` — Deletes current token, logs audit/activity
- `changePassword(ChangePasswordDTO): void` — Validates old password, updates, logs audit/activity
- `getCurrentUser(User): User` — Loads roles + permissions

---

#### `UserService` (`Services/UserService.php`)

| Attribute | Details |
|-----------|---------|
| **Dependencies** | `UserRepositoryInterface`, `RoleRepositoryInterface`, `AuditLogService`, `ActivityLogService` |
| **DTOs Used** | `RegisterUserDTO`, `VerifyUserDTO` |

**Business Logic:**
- CRUD with soft-delete support
- `create` — Sets status to `PENDING_VERIFICATION`, syncs roles
- `update` — Partial update, syncs roles, protects Super Admin
- `verify` — Changes status + `is_active` + `email_verified_at`
- `activate` / `deactivate` — Toggles status
- `resetPassword` — Generates random 12-char password
- `delete` / `restore` — Soft delete/restore with Super Admin protection
- All mutations log to AuditLog + ActivityLog

---

#### `SiteService` (`Services/SiteService.php`)

| Attribute | Details |
|-----------|---------|
| **Dependencies** | `SiteRepositoryInterface`, `AuditLogService`, `ActivityLogService` |
| **DTOs Used** | `SiteDTO` |

**Business Logic:** CRUD + restore with audit/activity logging.

---

#### `MaterialTypeService` (`Services/MaterialTypeService.php`)

| Attribute | Details |
|-----------|---------|
| **Dependencies** | `MaterialTypeRepositoryInterface`, `AuditLogService`, `ActivityLogService` |
| **DTOs Used** | `MaterialTypeDTO` |

**Business Logic:** CRUD + restore with audit/activity logging.

---

#### `MaterialModelService` (`Services/MaterialModelService.php`)

| Attribute | Details |
|-----------|---------|
| **Dependencies** | `MaterialModelRepositoryInterface`, `AuditLogService`, `ActivityLogService` |
| **DTOs Used** | `MaterialModelDTO` |

**Business Logic:** CRUD + restore + `findByMaterialTypeId` with audit/activity logging.

---

#### `MaterialService` (`Services/MaterialService.php`)

| Attribute | Details |
|-----------|---------|
| **Dependencies** | `MaterialRepositoryInterface`, `AuditLogService`, `ActivityLogService` |
| **DTOs Used** | `MaterialDTO` |

**Business Logic:** CRUD + restore with audit/activity logging.

---

#### `BarcodeService` (`Services/BarcodeService.php`)

| Attribute | Details |
|-----------|---------|
| **Dependencies** | `BarcodeRepositoryInterface`, `BarcodeHistoryService`, `AuditLogService`, `ActivityLogService` |
| **DTOs Used** | `BarcodeDTO`, `UpdateBarcodeDTO` |

**Business Logic:**
- `generateBarcodeId(): string` — Format: `BRC-YYYYMMDD-NNN`
- `create` — Generates barcode ID, creates history entry (CREATE), logs audit/activity
- `update` — Tracks field-level changes via `getChanges()`, creates history entries per changed field, logs audit/activity
- `delete` — Soft delete, creates history entry (SOFT_DELETE)
- `restore` — Restores, creates history entry (RESTORE)
- `getChanges()` — Compares old/new values for tracked fields: `material_id`, `site_id`, `serial_number`, `status`, `description`

---

#### `BarcodeHistoryService` (`Services/BarcodeHistoryService.php`)

| Attribute | Details |
|-----------|---------|
| **Dependencies** | `BarcodeHistoryRepositoryInterface` |

**Business Logic:** Delegates to repository for `findByBarcodeId`, `findAllPaginatedByBarcode`, `create`.

---

#### `RoleService` (`Services/RoleService.php`)

| Attribute | Details |
|-----------|---------|
| **Dependencies** | `RoleRepositoryInterface`, `AuditLogService`, `ActivityLogService` |

**Business Logic:** CRUD + restore, syncs permissions on create/update, logs audit/activity.

---

#### `PermissionService` (`Services/PermissionService.php`)

| Attribute | Details |
|-----------|---------|
| **Dependencies** | `PermissionRepositoryInterface`, `AuditLogService`, `ActivityLogService` |

**Business Logic:** CRUD + restore + `findByModule`, logs audit/activity.

---

#### `AuditLogService` (`Services/AuditLogService.php`)

| Attribute | Details |
|-----------|---------|
| **Dependencies** | `AuditLogRepositoryInterface`, `Request` |

**Methods:**
- `log(?int $userId, string $entityType, string $entityId, string $action, ?array $oldValues, ?array $newValues): void`

**Used By:** All other services (AuthenticationService, UserService, SiteService, MaterialTypeService, MaterialModelService, MaterialService, BarcodeService, RoleService, PermissionService)

---

#### `ActivityLogService` (`Services/ActivityLogService.php`)

| Attribute | Details |
|-----------|---------|
| **Dependencies** | `ActivityLogRepositoryInterface`, `Request` |

**Methods:**
- `log(?int $userId, string $activity, string $module, ?string $description, ?string $sessionId): void`

**Used By:** All other services (same as AuditLogService)

---

### Controllers

#### `AuthController` (`Http/Controllers/AuthController.php`)

| Route | Method | Request | DTO | Service | Resource |
|-------|--------|---------|-----|---------|----------|
| `POST /api/auth/login` | `login` | `LoginRequest` | `LoginRequestDTO` | `AuthenticationService` | `UserResource` |
| `POST /api/auth/logout` | `logout` | `Request` | — | `AuthenticationService` | — |
| `GET /api/auth/me` | `me` | `Request` | — | `AuthenticationService` | `UserResource` |
| `POST /api/auth/change-password` | `changePassword` | `ChangePasswordRequest` | `ChangePasswordDTO` | `AuthenticationService` | — |

---

#### `UserController` (`Http/Controllers/UserController.php`)

| Route | Method | Request | DTO | Service | Resource |
|-------|--------|---------|-----|---------|----------|
| `GET /api/users` | `index` | `Request` | — | `UserService` | `UserResource` |
| `GET /api/users/{id}` | `show` | — | — | `UserService` | `UserResource` |
| `POST /api/users` | `store` | `CreateUserRequest` | `RegisterUserDTO` | `UserService` | `UserResource` |
| `PUT /api/users/{id}` | `update` | `UpdateUserRequest` | — | `UserService` | `UserResource` |
| `DELETE /api/users/{id}` | `destroy` | — | — | `UserService` | — |
| `POST /api/users/{id}/restore` | `restore` | — | — | `UserService` | — |
| `POST /api/users/{id}/verify` | `verify` | `VerifyUserRequest` | `VerifyUserDTO` | `UserService` | `UserResource` |
| `POST /api/users/{id}/activate` | `activate` | — | — | `UserService` | `UserResource` |
| `POST /api/users/{id}/deactivate` | `deactivate` | — | — | `UserService` | `UserResource` |
| `POST /api/users/{id}/reset-password` | `resetPassword` | — | — | `UserService` | — |

---

#### `RoleController` (`Http/Controllers/RoleController.php`)

| Route | Method | Request | Service | Resource |
|-------|--------|---------|---------|----------|
| `GET /api/roles` | `index` | `Request` | `RoleService` | `RoleResource` |
| `GET /api/roles/all` | `all` | — | `RoleService` | `RoleResource` |
| `GET /api/roles/{id}` | `show` | — | `RoleService` | `RoleResource` |
| `POST /api/roles` | `store` | `StoreRoleRequest` | `RoleService` | `RoleResource` |
| `PUT /api/roles/{id}` | `update` | `UpdateRoleRequest` | `RoleService` | `RoleResource` |
| `DELETE /api/roles/{id}` | `destroy` | — | `RoleService` | — |
| `POST /api/roles/{id}/restore` | `restore` | — | `RoleService` | — |

---

#### `PermissionController` (`Http/Controllers/PermissionController.php`)

| Route | Method | Request | Service | Resource |
|-------|--------|---------|---------|----------|
| `GET /api/permissions` | `index` | `Request` | `PermissionService` | `PermissionResource` |
| `GET /api/permissions/all` | `all` | — | `PermissionService` | `PermissionResource` |
| `GET /api/permissions/{id}` | `show` | — | `PermissionService` | `PermissionResource` |
| `POST /api/permissions` | `store` | `StorePermissionRequest` | `PermissionService` | `PermissionResource` |
| `PUT /api/permissions/{id}` | `update` | `UpdatePermissionRequest` | `PermissionService` | `PermissionResource` |
| `DELETE /api/permissions/{id}` | `destroy` | — | `PermissionService` | — |
| `POST /api/permissions/{id}/restore` | `restore` | — | `PermissionService` | — |

---

#### `SiteController` (`Http/Controllers/SiteController.php`)

| Route | Method | Request | Service | Resource |
|-------|--------|---------|---------|----------|
| `GET /api/sites` | `index` | `Request` | `SiteService` | `SiteResource` |
| `GET /api/sites/all` | `all` | — | `SiteService` | `SiteResource` |
| `GET /api/sites/{id}` | `show` | — | `SiteService` | `SiteResource` |
| `POST /api/sites` | `store` | `StoreSiteRequest` | `SiteService` | `SiteResource` |
| `PUT /api/sites/{id}` | `update` | `UpdateSiteRequest` | `SiteService` | `SiteResource` |
| `DELETE /api/sites/{id}` | `destroy` | — | `SiteService` | — |
| `POST /api/sites/{id}/restore` | `restore` | — | `SiteService` | — |

---

#### `MaterialTypeController` (`Http/Controllers/MaterialTypeController.php`)

| Route | Method | Request | Service | Resource |
|-------|--------|---------|---------|----------|
| `GET /api/material-types` | `index` | `Request` | `MaterialTypeService` | `MaterialTypeResource` |
| `GET /api/material-types/all` | `all` | — | `MaterialTypeService` | `MaterialTypeResource` |
| `GET /api/material-types/{id}` | `show` | — | `MaterialTypeService` | `MaterialTypeResource` |
| `POST /api/material-types` | `store` | `StoreMaterialTypeRequest` | `MaterialTypeService` | `MaterialTypeResource` |
| `PUT /api/material-types/{id}` | `update` | `UpdateMaterialTypeRequest` | `MaterialTypeService` | `MaterialTypeResource` |
| `DELETE /api/material-types/{id}` | `destroy` | — | `MaterialTypeService` | — |
| `POST /api/material-types/{id}/restore` | `restore` | — | `MaterialTypeService` | — |

---

#### `MaterialModelController` (`Http/Controllers/MaterialModelController.php`)

| Route | Method | Request | Service | Resource |
|-------|--------|---------|---------|----------|
| `GET /api/material-models` | `index` | `Request` | `MaterialModelService` | `MaterialModelResource` |
| `GET /api/material-models/all` | `all` | — | `MaterialModelService` | `MaterialModelResource` |
| `GET /api/material-models/by-type/{materialTypeId}` | `byMaterialType` | — | `MaterialModelService` | `MaterialModelResource` |
| `GET /api/material-models/{id}` | `show` | — | `MaterialModelService` | `MaterialModelResource` |
| `POST /api/material-models` | `store` | `StoreMaterialModelRequest` | `MaterialModelService` | `MaterialModelResource` |
| `PUT /api/material-models/{id}` | `update` | `UpdateMaterialModelRequest` | `MaterialModelService` | `MaterialModelResource` |
| `DELETE /api/material-models/{id}` | `destroy` | — | `MaterialModelService` | — |
| `POST /api/material-models/{id}/restore` | `restore` | — | `MaterialModelService` | — |

---

#### `MaterialController` (`Http/Controllers/MaterialController.php`)

| Route | Method | Request | Service | Resource |
|-------|--------|---------|---------|----------|
| `GET /api/materials` | `index` | `Request` | `MaterialService` | `MaterialResource` |
| `GET /api/materials/all` | `all` | — | `MaterialService` | `MaterialResource` |
| `GET /api/materials/{id}` | `show` | — | `MaterialService` | `MaterialResource` |
| `POST /api/materials` | `store` | `StoreMaterialRequest` | `MaterialService` | `MaterialResource` |
| `PUT /api/materials/{id}` | `update` | `UpdateMaterialRequest` | `MaterialService` | `MaterialResource` |
| `DELETE /api/materials/{id}` | `destroy` | — | `MaterialService` | — |
| `POST /api/materials/{id}/restore` | `restore` | — | `MaterialService` | — |

---

#### `BarcodeController` (`Http/Controllers/BarcodeController.php`)

| Route | Method | Request | Service | Resource |
|-------|--------|---------|---------|----------|
| `GET /api/barcodes` | `index` | `Request` | `BarcodeService` | `BarcodeResource` |
| `GET /api/barcodes/all` | `all` | — | `BarcodeService` | `BarcodeResource` |
| `GET /api/barcodes/{id}` | `show` | — | `BarcodeService` | `BarcodeDetailResource` |
| `GET /api/barcodes/by-barcode-id/{barcodeId}` | `showByBarcodeId` | — | `BarcodeService` | `BarcodeDetailResource` |
| `POST /api/barcodes` | `store` | `StoreBarcodeRequest` | `BarcodeService` | `BarcodeResource` |
| `PUT /api/barcodes/{id}` | `update` | `UpdateBarcodeRequest` | `BarcodeService` | `BarcodeResource` |
| `DELETE /api/barcodes/{id}` | `destroy` | — | `BarcodeService` | — |
| `POST /api/barcodes/{id}/restore` | `restore` | — | `BarcodeService` | — |
| `GET /api/barcodes/{id}/history` | `history` | `Request` | `BarcodeHistoryService` | `BarcodeHistoryResource` |

---

### Requests (Form Requests)

| Request | Path | Validation Rules |
|---------|------|------------------|
| `LoginRequest` | `Http/Requests/LoginRequest.php` | `login` (required, string), `password` (required, string), `remember` (boolean) |
| `ChangePasswordRequest` | `Http/Requests/ChangePasswordRequest.php` | `current_password` (required), `new_password` (required, min:8, confirmed) |
| `CreateUserRequest` | `Http/Requests/CreateUserRequest.php` | `username` (unique), `email` (unique), `password` (min:8, confirmed), `full_name`, `role_ids` (array, exists) |
| `UpdateUserRequest` | `Http/Requests/UpdateUserRequest.php` | `username` (unique, ignore current), `email` (unique, ignore current), `full_name`, `role_ids` (array, exists) |
| `VerifyUserRequest` | `Http/Requests/VerifyUserRequest.php` | `status` (required, in: active/inactive/suspended) |
| `StoreSiteRequest` | `Http/Requests/StoreSiteRequest.php` | `site_id` (required, unique), `site_name` (required), `region`, `address`, `latitude`, `longitude` |
| `UpdateSiteRequest` | `Http/Requests/UpdateSiteRequest.php` | `site_id` (unique, ignore current), `site_name`, `region`, `address`, `latitude`, `longitude` |
| `StoreMaterialTypeRequest` | `Http/Requests/StoreMaterialTypeRequest.php` | `name` (required, unique), `description` |
| `UpdateMaterialTypeRequest` | `Http/Requests/UpdateMaterialTypeRequest.php` | `name` (unique, ignore current), `description` |
| `StoreMaterialModelRequest` | `Http/Requests/StoreMaterialModelRequest.php` | `material_type_id` (required, exists), `name` (required), `description` |
| `UpdateMaterialModelRequest` | `Http/Requests/UpdateMaterialModelRequest.php` | `material_type_id` (exists), `name`, `description` |
| `StoreMaterialRequest` | `Http/Requests/StoreMaterialRequest.php` | `material_type_id` (required, exists), `material_model_id` (required, exists), `material_code` (required, unique), `name` (required), `description` |
| `UpdateMaterialRequest` | `Http/Requests/UpdateMaterialRequest.php` | `material_type_id` (exists), `material_model_id` (exists), `material_code` (unique, ignore current), `name`, `description` |
| `StoreBarcodeRequest` | `Http/Requests/StoreBarcodeRequest.php` | `material_id` (required, exists), `site_id` (required, exists), `serial_number` (required, unique), `status` (required, in: NEW/OLD), `description` |
| `UpdateBarcodeRequest` | `Http/Requests/UpdateBarcodeRequest.php` | `material_id` (exists), `site_id` (exists), `serial_number` (unique, ignore current), `status` (in: NEW/OLD), `description` |
| `StoreRoleRequest` | `Http/Requests/StoreRoleRequest.php` | `name` (required, unique), `display_name` (required), `description`, `permission_ids` (array, exists) |
| `UpdateRoleRequest` | `Http/Requests/UpdateRoleRequest.php` | `name` (unique, ignore current), `display_name`, `description`, `permission_ids` (array, exists) |
| `StorePermissionRequest` | `Http/Requests/StorePermissionRequest.php` | `name` (required, unique), `display_name` (required), `module` (required), `description` |
| `UpdatePermissionRequest` | `Http/Requests/UpdatePermissionRequest.php` | `name` (unique, ignore current), `display_name`, `module`, `description` |

---

### Resources (API Transformers)

| Resource | Path | Attributes Returned |
|----------|------|---------------------|
| `UserResource` | `Http/Resources/UserResource.php` | `id`, `username`, `email`, `full_name`, `status`, `is_active`, `email_verified_at`, `last_login_at`, `password_changed_at`, `created_at`, `updated_at`, `roles` (nested), `permissions` (nested) |
| `RoleResource` | `Http/Resources/RoleResource.php` | `id`, `name`, `display_name`, `description`, `is_active`, `created_at`, `updated_at`, `permissions` (nested) |
| `PermissionResource` | `Http/Resources/PermissionResource.php` | `id`, `name`, `display_name`, `module`, `description`, `is_active`, `created_at`, `updated_at` |
| `SiteResource` | `Http/Resources/SiteResource.php` | `id`, `site_id`, `site_name`, `region`, `address`, `latitude`, `longitude`, `is_active`, `created_at`, `updated_at` |
| `MaterialTypeResource` | `Http/Resources/MaterialTypeResource.php` | `id`, `name`, `description`, `is_active`, `created_at`, `updated_at` |
| `MaterialModelResource` | `Http/Resources/MaterialModelResource.php` | `id`, `material_type_id`, `name`, `description`, `is_active`, `created_at`, `updated_at`, `material_type` (nested) |
| `MaterialResource` | `Http/Resources/MaterialResource.php` | `id`, `material_type_id`, `material_model_id`, `material_code`, `name`, `description`, `is_active`, `created_at`, `updated_at`, `material_type` (nested), `material_model` (nested) |
| `BarcodeResource` | `Http/Resources/BarcodeResource.php` | `id`, `barcode_id`, `serial_number`, `status`, `description`, `is_active`, `created_at`, `updated_at`, `material` (nested), `site` (nested), `created_by` (nested), `updated_by` (nested) |
| `BarcodeDetailResource` | `Http/Resources/BarcodeDetailResource.php` | Same as BarcodeResource + `histories` (nested with `changedBy`) |
| `BarcodeHistoryResource` | `Http/Resources/BarcodeHistoryResource.php` | `id`, `barcode_id`, `field_name`, `old_value`, `new_value`, `changed_by`, `change_reason`, `created_at`, `changed_by` (nested user) |

---

### Middleware

| Middleware | Path | Purpose |
|------------|------|---------|
| `CheckPermission` | `Http/Middleware/CheckPermission.php` | Checks if authenticated user has a specific permission via `$user->hasPermission($permission)` |

**Registration** (`bootstrap/app.php`):
- Alias: `'permission' => \App\Http\Middleware\CheckPermission::class`
- Used in routes via: `->middleware('permission:read-barcode')`

---

### Providers

| Provider | Path | Purpose |
|----------|------|---------|
| `AppServiceProvider` | `Providers/AppServiceProvider.php` | Binds all Repository Interfaces to their implementations |

**Bindings Registered:**

| Interface | Implementation |
|-----------|---------------|
| `UserRepositoryInterface` | `UserRepository` |
| `SiteRepositoryInterface` | `SiteRepository` |
| `MaterialTypeRepositoryInterface` | `MaterialTypeRepository` |
| `MaterialModelRepositoryInterface` | `MaterialModelRepository` |
| `MaterialRepositoryInterface` | `MaterialRepository` |
| `BarcodeRepositoryInterface` | `BarcodeRepository` |
| `BarcodeHistoryRepositoryInterface` | `BarcodeHistoryRepository` |
| `RoleRepositoryInterface` | `RoleRepository` |
| `PermissionRepositoryInterface` | `PermissionRepository` |
| `AuditLogRepositoryInterface` | `AuditLogRepository` |
| `ActivityLogRepositoryInterface` | `ActivityLogRepository` |

---

### Routes

**File:** `routes/api.php`

**Route Groups:**

| Prefix | Middleware | Routes |
|--------|-----------|--------|
| `/api/auth` | `throttle:5,1` (login only) | `login`, `logout`, `me`, `change-password` |
| `/api/users` | `auth:sanctum` + `permission:*` | CRUD + `verify`, `activate`, `deactivate`, `reset-password`, `restore` |
| `/api/roles` | `auth:sanctum` + `permission:*` | CRUD + `all`, `restore` |
| `/api/permissions` | `auth:sanctum` + `permission:*` | CRUD + `all`, `restore` |
| `/api/sites` | `auth:sanctum` + `permission:*` | CRUD + `all`, `restore` |
| `/api/material-types` | `auth:sanctum` + `permission:*` | CRUD + `all`, `restore` |
| `/api/material-models` | `auth:sanctum` + `permission:*` | CRUD + `all`, `by-type`, `restore` |
| `/api/materials` | `auth:sanctum` + `permission:*` | CRUD + `all`, `restore` |
| `/api/barcodes` | `auth:sanctum` + `permission:*` | CRUD + `all`, `by-barcode-id`, `history`, `restore` |

---

## Frontend

### Overview

| Attribute | Value |
|-----------|-------|
| **Framework** | React 18 |
| **Build Tool** | Vite |
| **Styling** | Tailwind CSS |
| **Entry Point** | `frontend/index.html` → `frontend/src/main.jsx` |
| **Config Files** | `vite.config.js`, `tailwind.config.js`, `postcss.config.js`, `components.json`, `package.json` |

### Configuration

| File | Key Details |
|------|-------------|
| `vite.config.js` | Plugin: `@vitejs/plugin-react`, Server: `host: 0.0.0.0, port: 5173` |
| `tailwind.config.js` | Content: `['./index.html', './src/**/*.{js,ts,jsx,tsx}']` |
| `postcss.config.js` | Plugins: `tailwindcss`, `autoprefixer` |
| `package.json` | Dependencies include React, Tailwind, Vite |

### Source Structure (`frontend/src/`)

```
frontend/src/
├── main.jsx          # React entry point
├── App.jsx           # Root component
├── components/       # Reusable UI components
├── pages/            # Page-level components
├── hooks/            # Custom React hooks
├── services/         # API service layer
└── ...               # Additional modules
```

*(Detailed frontend structure to be expanded as frontend development progresses)*

---

## Documentation

### `docs/` Directory

| File | Purpose |
|------|---------|
| `AI_CONTEXT.md` | AI assistant context and guidelines |
| `API.md` | API endpoint documentation |
| `AUDIT_LOG_RULES.md` | Audit logging rules and conventions |
| `AUTH_RULES.md` | Authentication and authorization rules |
| `BARCODE_DATABASE_SPEC.md` | Barcode database specification |
| `BARCODE_RULES.md` | Barcode business rules |
| `BUSINESS_RULES.md` | General business rules |
| `CODING_STANDARD.md` | Coding standards and conventions |
| `DATABASE_DESIGN.md` | Database design documentation |
| `DATABASE_FINAL.md` | Final database schema |
| `DATABASE.md` | General database documentation |
| `ERD.md` | Entity Relationship Diagram |
| `FEATURE_GENERATE_BARCODE.md` | Barcode generation feature spec |
| `FILTER_RULES.md` | Filtering rules and conventions |
| `FOLDER_STRUCTURE.md` | Project folder structure |
| `HISTORY_RULES.md` | History tracking rules |
| `MATERIAL_RULES.md` | Material management rules |
| `MENU_STRUCTURE.md` | Menu structure documentation |
| `MIGRATION_PLAN.md` | Database migration plan |
| `PERMISSION_MATRIX.md` | Permission matrix |
| `PRD.md` | Product Requirements Document |
| `PROJECT_OVERVIEW.md` | Project overview |
| `PROJECT_STATUS.md` | Current project status |
| `SITE_RULES.md` | Site management rules |
| `SPREADSHEET_RULES.md` | Spreadsheet export rules |
| `UI_BARCODE_DETAIL.md` | Barcode detail UI spec |
| `UI_BARCODE_FORM.md` | Barcode form UI spec |
| `UI_BARCODE_LIST.md` | Barcode list UI spec |
| `UI_DASHBOARD.md` | Dashboard UI spec |
| `UI_LOGIN.md` | Login page UI spec |
| `UI_MASTER_MATERIAL.md` | Master material UI spec |
| `UI_MASTER_SITE.md` | Master site UI spec |
| `UI_USER_MANAGEMENT.md` | User management UI spec |
| `UI.md` | General UI documentation |
| `USER_RULES.md` | User management rules |
| `VALIDATION_RULES.md` | Validation rules |
| `WORKFLOW_BARCODE.md` | Barcode workflow documentation |

### `docs/project/` Directory

| File | Purpose |
|------|---------|
| `PROJECT_INDEX.md` | **(This file)** Complete repository index for audits |

---

## Tasks

### `tasks/` Directory

| File | Phase | Description |
|------|-------|-------------|
| `PHASE-1-TASK-1.2.md` | Phase 1 | Initial project setup |
| `PHASE-1-TASK-1.3.md` | Phase 1 | Project configuration |
| `PHASE-1-TASK-1.4.md` | Phase 1 | Environment setup |
| `PHASE-2-TASK-2.1.md` | Phase 2 | Database schema design |
| `PHASE-2-TASK-2.2.md` | Phase 2 | Migration implementation |
| `PHASE-2-TASK-2.3.md` | Phase 2 | Seeder implementation |
| `PHASE-2-TASK-2.4.md` | Phase 2 | Model implementation |
| `PHASE-2-TASK-2.5.md` | Phase 2 | Repository pattern setup |
| `PHASE-2-TASK-2.6.md` | Phase 2 | Service layer setup |
| `PHASE-2-TASK-2.7.md` | Phase 2 | Controller setup |
| `PHASE-2-TASK-2.8.md` | Phase 2 | API resource setup |
| `PHASE-2-TASK-2.9.md` | Phase 2 | Validation setup |
| `PHASE-2-TASK-2.10.md` | Phase 2 | Route setup |
| `PHASE-2-TASK-2.11.md` | Phase 2 | DTO implementation |
| `PHASE-2-TASK-2.12.md` | Phase 2 | Enum implementation |
| `PHASE-2-TASK-2.13.md` | Phase 2 | Interface implementation |
| `PHASE-2-TASK-2.14.md` | Phase 2 | Testing setup |
| `PHASE-2.5-TASK-2.15.md` | Phase 2.5 | Additional refinements |
| `PHASE-3-AUTH-REVIEW.md` | Phase 3 | Auth module review |
| `PHASE-3-MODULE-AUTH.md` | Phase 3 | Auth module implementation |
| `PHASE-3-TASK-3.1.md` | Phase 3 | Auth task 1 |
| `PHASE-3-TASK-3.2.md` | Phase 3 | Auth task 2 |
| `PHASE-3-TASK-3.3.md` | Phase 3 | Auth task 3 |
| `PHASE-3-TASK-3.4.md` | Phase 3 | Auth task 4 |
| `PHASE-4-INTEGRATION-REVIEW.md` | Phase 4 | Integration review |
| `PHASE-4-MODULE-MASTER-DATA.md` | Phase 4 | Master data module |
| `PHASE-4-STABILIZATION.md` | Phase 4 | Stabilization tasks |
| `PHASE-5-BARCODE-REVIEW.md` | Phase 5 | Barcode module review |
| `PHASE-5-MODULE-BARCODE.md` | Phase 5 | Barcode module implementation |
| `TASK-001.md` | — | General task 1 |
| `TASK-002.md` | — | General task 2 |
| `TASK-003.md` | — | General task 3 |

---

## Infrastructure

### Docker

**File:** `docker-compose.yml`

| Service | Image | Ports | Purpose |
|---------|-------|-------|---------|
| `app` | PHP 8.2 FPM | — | Laravel application server |
| `nginx` | Nginx | `80:80` | Web server |
| `postgres` | PostgreSQL 15 | `5432:5432` | Database |
| `frontend` | Node | `5173:5173` | Vite dev server |

### Environment

**File:** `backend/.env.example`

| Variable | Value |
|----------|-------|
| `APP_NAME` | Barcode Management System |
| `APP_ENV` | local |
| `APP_DEBUG` | true |
| `APP_URL` | http://localhost |
| `DB_CONNECTION` | pgsql |
| `DB_HOST` | postgres |
| `DB_PORT` | 5432 |
| `DB_DATABASE` | barcode_management |
| `DB_USERNAME` | barcode_user |
| `DB_PASSWORD` | barcode_password |
| `CACHE_DRIVER` | file |
| `QUEUE_CONNECTION` | sync |
| `SESSION_DRIVER` | file |

### Git

| Attribute | Value |
|-----------|-------|
| **Remote** | `origin → https://github.com/narcen12345-a11y/Barcode-Management-System-2.git` |
| **Branch** | `main` |
| **Ignored** | `vendor/`, `node_modules/`, `.env`, `storage/`, `dist/`, `.idea/`, `.vscode/`, logs |

---

## Dependency Graph

```
┌─────────────────────────────────────────────────────────────┐
│                        Routes (api.php)                      │
├─────────────────────────────────────────────────────────────┤
│  Controllers ←→ Requests (validation) → Resources (output)  │
│       │                                                     │
│       ▼                                                     │
│  Services (business logic) ←→ DTOs (data transfer)          │
│       │                                                     │
│       ▼                                                     │
│  Repositories (data access) ←→ Interfaces (contracts)       │
│       │                                                     │
│       ▼                                                     │
│  Models (Eloquent ORM) ←→ Database (PostgreSQL)             │
│       │                                                     │
│       ▼                                                     │
│  AuditLogService / ActivityLogService (cross-cutting)       │
└─────────────────────────────────────────────────────────────┘
```

---

## Architecture Metadata


### Controllers — Detailed Metadata

#### `AuthController` (`Http/Controllers/AuthController.php`)

| Attribute | Value |
|-----------|-------|
| **Route Prefix** | `/api/auth` |
| **Middleware** | `throttle:5,1` (login only) |
| **Dependencies** | `AuthenticationService` |
| **Methods** | `login`, `logout`, `me`, `changePassword` |

| Method | Request | DTO | Service | Resource |
|--------|---------|-----|---------|----------|
| `login` | `LoginRequest` | `LoginRequestDTO` | `AuthenticationService` | `UserResource` |
| `logout` | `Request` | — | `AuthenticationService` | — |
| `me` | `Request` | — | `AuthenticationService` | `UserResource` |
| `changePassword` | `ChangePasswordRequest` | `ChangePasswordDTO` | `AuthenticationService` | — |

---

#### `UserController` (`Http/Controllers/UserController.php`)

| Attribute | Value |
|-----------|-------|
| **Route Prefix** | `/api/users` |
| **Middleware** | `auth:sanctum`, `permission:*` |
| **Dependencies** | `UserService` |
| **Methods** | `index`, `show`, `store`, `update`, `destroy`, `restore`, `verify`, `activate`, `deactivate`, `resetPassword` |

| Method | Request | DTO | Service | Resource |
|--------|---------|-----|---------|----------|
| `index` | `Request` | — | `UserService` | `UserResource` |
| `show` | — | — | `UserService` | `UserResource` |
| `store` | `CreateUserRequest` | `RegisterUserDTO` | `UserService` | `UserResource` |
| `update` | `UpdateUserRequest` | — | `UserService` | `UserResource` |
| `destroy` | — | — | `UserService` | — |
| `restore` | — | — | `UserService` | — |
| `verify` | `VerifyUserRequest` | `VerifyUserDTO` | `UserService` | `UserResource` |
| `activate` | — | — | `UserService` | `UserResource` |
| `deactivate` | — | — | `UserService` | `UserResource` |
| `resetPassword` | — | — | `UserService` | — |

---

#### `RoleController` (`Http/Controllers/RoleController.php`)

| Attribute | Value |
|-----------|-------|
| **Route Prefix** | `/api/roles` |
| **Middleware** | `auth:sanctum`, `permission:*` |
| **Dependencies** | `RoleService` |
| **Methods** | `index`, `all`, `show`, `store`, `update`, `destroy`, `restore` |

| Method | Request | DTO | Service | Resource |
|--------|---------|-----|---------|----------|
| `index` | `Request` | — | `RoleService` | `RoleResource` |
| `all` | — | — | `RoleService` | `RoleResource` |
| `show` | — | — | `RoleService` | `RoleResource` |
| `store` | `StoreRoleRequest` | — | `RoleService` | `RoleResource` |
| `update` | `UpdateRoleRequest` | — | `RoleService` | `RoleResource` |
| `destroy` | — | — | `RoleService` | — |
| `restore` | — | — | `RoleService` | — |

---

#### `PermissionController` (`Http/Controllers/PermissionController.php`)

| Attribute | Value |
|-----------|-------|
| **Route Prefix** | `/api/permissions` |
| **Middleware** | `auth:sanctum`, `permission:*` |
| **Dependencies** | `PermissionService` |
| **Methods** | `index`, `all`, `show`, `store`, `update`, `destroy`, `restore` |

| Method | Request | DTO | Service | Resource |
|--------|---------|-----|---------|----------|
| `index` | `Request` | — | `PermissionService` | `PermissionResource` |
| `all` | — | — | `PermissionService` | `PermissionResource` |
| `show` | — | — | `PermissionService` | `PermissionResource` |
| `store` | `StorePermissionRequest` | — | `PermissionService` | `PermissionResource` |
| `update` | `UpdatePermissionRequest` | — | `PermissionService` | `PermissionResource` |
| `destroy` | — | — | `PermissionService` | — |
| `restore` | — | — | `PermissionService` | — |

---

#### `SiteController` (`Http/Controllers/SiteController.php`)

| Attribute | Value |
|-----------|-------|
| **Route Prefix** | `/api/sites` |
| **Middleware** | `auth:sanctum`, `permission:*` |
| **Dependencies** | `SiteService` |
| **Methods** | `index`, `all`, `show`, `store`, `update`, `destroy`, `restore` |

| Method | Request | DTO | Service | Resource |
|--------|---------|-----|---------|----------|
| `index` | `Request` | — | `SiteService` | `SiteResource` |
| `all` | — | — | `SiteService` | `SiteResource` |
| `show` | — | — | `SiteService` | `SiteResource` |
| `store` | `StoreSiteRequest` | — | `SiteService` | `SiteResource` |
| `update` | `UpdateSiteRequest` | — | `SiteService` | `SiteResource` |
| `destroy` | — | — | `SiteService` | — |
| `restore` | — | — | `SiteService` | — |

---

#### `MaterialTypeController` (`Http/Controllers/MaterialTypeController.php`)

| Attribute | Value |
|-----------|-------|
| **Route Prefix** | `/api/material-types` |
| **Middleware** | `auth:sanctum`, `permission:*` |
| **Dependencies** | `MaterialTypeService` |
| **Methods** | `index`, `all`, `show`, `store`, `update`, `destroy`, `restore` |

| Method | Request | DTO | Service | Resource |
|--------|---------|-----|---------|----------|
| `index` | `Request` | — | `MaterialTypeService` | `MaterialTypeResource` |
| `all` | — | — | `MaterialTypeService` | `MaterialTypeResource` |
| `show` | — | — | `MaterialTypeService` | `MaterialTypeResource` |
| `store` | `StoreMaterialTypeRequest` | — | `MaterialTypeService` | `MaterialTypeResource` |
| `update` | `UpdateMaterialTypeRequest` | — | `MaterialTypeService` | `MaterialTypeResource` |
| `destroy` | — | — | `MaterialTypeService` | — |
| `restore` | — | — | `MaterialTypeService` | — |

---

#### `MaterialModelController` (`Http/Controllers/MaterialModelController.php`)

| Attribute | Value |
|-----------|-------|
| **Route Prefix** | `/api/material-models` |
| **Middleware** | `auth:sanctum`, `permission:*` |
| **Dependencies** | `MaterialModelService` |
| **Methods** | `index`, `all`, `byMaterialType`, `show`, `store`, `update`, `destroy`, `restore` |

| Method | Request | DTO | Service | Resource |
|--------|---------|-----|---------|----------|
| `index` | `Request` | — | `MaterialModelService` | `MaterialModelResource` |
| `all` | — | — | `MaterialModelService` | `MaterialModelResource` |
| `byMaterialType` | — | — | `MaterialModelService` | `MaterialModelResource` |
| `show` | — | — | `MaterialModelService` | `MaterialModelResource` |
| `store` | `StoreMaterialModelRequest` | — | `MaterialModelService` | `MaterialModelResource` |
| `update` | `UpdateMaterialModelRequest` | — | `MaterialModelService` | `MaterialModelResource` |
| `destroy` | — | — | `MaterialModelService` | — |
| `restore` | — | — | `MaterialModelService` | — |

---

#### `MaterialController` (`Http/Controllers/MaterialController.php`)

| Attribute | Value |
|-----------|-------|
| **Route Prefix** | `/api/materials` |
| **Middleware** | `auth:sanctum`, `permission:*` |
| **Dependencies** | `MaterialService` |
| **Methods** | `index`, `all`, `show`, `store`, `update`, `destroy`, `restore` |

| Method | Request | DTO | Service | Resource |
|--------|---------|-----|---------|----------|
| `index` | `Request` | — | `MaterialService` | `MaterialResource` |
| `all` | — | — | `MaterialService` | `MaterialResource` |
| `show` | — | — | `MaterialService` | `MaterialResource` |
| `store` | `StoreMaterialRequest` | — | `MaterialService` | `MaterialResource` |
| `update` | `UpdateMaterialRequest` | — | `MaterialService` | `MaterialResource` |
| `destroy` | — | — | `MaterialService` | — |
| `restore` | — | — | `MaterialService` | — |

---

#### `BarcodeController` (`Http/Controllers/BarcodeController.php`)

| Attribute | Value |
|-----------|-------|
| **Route Prefix** | `/api/barcodes` |
| **Middleware** | `auth:sanctum`, `permission:*` |
| **Dependencies** | `BarcodeService`, `BarcodeHistoryService` |
| **Methods** | `index`, `all`, `show`, `showByBarcodeId`, `store`, `update`, `destroy`, `restore`, `history` |

| Method | Request | DTO | Service | Resource |
|--------|---------|-----|---------|----------|
| `index` | `Request` | — | `BarcodeService` | `BarcodeResource` |
| `all` | — | — | `BarcodeService` | `BarcodeResource` |
| `show` | — | — | `BarcodeService` | `BarcodeDetailResource` |
| `showByBarcodeId` | — | — | `BarcodeService` | `BarcodeDetailResource` |
| `store` | `StoreBarcodeRequest` | `BarcodeDTO` | `BarcodeService` | `BarcodeResource` |
| `update` | `UpdateBarcodeRequest` | `UpdateBarcodeDTO` | `BarcodeService` | `BarcodeResource` |
| `destroy` | — | — | `BarcodeService` | — |
| `restore` | — | — | `BarcodeService` | — |
| `history` | `Request` | — | `BarcodeHistoryService` | `BarcodeHistoryResource` |

---

### Services — Detailed Metadata

#### `AuthenticationService`

| Attribute | Value |
|-----------|-------|
| **Repository Dependencies** | `UserRepositoryInterface` |
| **Transaction Usage** | Yes — `login`, `logout`, `changePassword` |
| **Audit Logging** | Yes — `failed_login`, `login`, `logout`, `change_password` |
| **Event Dispatch** | None |
| **Return Types** | `login(): array`, `logout(): void`, `changePassword(): void`, `getCurrentUser(): User` |

---

#### `UserService`

| Attribute | Value |
|-----------|-------|
| **Repository Dependencies** | `UserRepositoryInterface`, `RoleRepositoryInterface` |
| **Transaction Usage** | Yes — all mutating methods (`create`, `update`, `verify`, `activate`, `deactivate`, `resetPassword`, `delete`, `restore`) |
| **Audit Logging** | Yes — `create_user`, `update_user`, `verify_user`, `activate_user`, `deactivate_user`, `reset_password`, `delete_user`, `restore_user` |
| **Event Dispatch** | None |
| **Return Types** | `findAllPaginated(): LengthAwarePaginator`, `findAll(): Collection`, `findById(): ?User`, `create(): User`, `update(): User`, `verify(): User`, `activate(): User`, `deactivate(): User`, `resetPassword(): string`, `delete(): void`, `restore(): void`, `countByStatus(): int` |

---

#### `SiteService`

| Attribute | Value |
|-----------|-------|
| **Repository Dependencies** | `SiteRepositoryInterface` |
| **Transaction Usage** | Yes — all mutating methods (`create`, `update`, `delete`, `restore`) |
| **Audit Logging** | Yes — `create_site`, `update_site`, `delete_site`, `restore_site` |
| **Event Dispatch** | None |
| **Return Types** | `findAllPaginated(): LengthAwarePaginator`, `findAll(): Collection`, `findById(): ?Site`, `findBySiteId(): ?Site`, `create(): Site`, `update(): Site`, `delete(): void`, `restore(): void` |

---

#### `MaterialTypeService`

| Attribute | Value |
|-----------|-------|
| **Repository Dependencies** | `MaterialTypeRepositoryInterface` |
| **Transaction Usage** | Yes — all mutating methods (`create`, `update`, `delete`, `restore`) |
| **Audit Logging** | Yes — `create_material_type`, `update_material_type`, `delete_material_type`, `restore_material_type` |
| **Event Dispatch** | None |
| **Return Types** | `findAllPaginated(): LengthAwarePaginator`, `findAll(): Collection`, `findById(): ?MaterialType`, `findByName(): ?MaterialType`, `create(): MaterialType`, `update(): MaterialType`, `delete(): void`, `restore(): void` |

---

#### `MaterialModelService`

| Attribute | Value |
|-----------|-------|
| **Repository Dependencies** | `MaterialModelRepositoryInterface` |
| **Transaction Usage** | Yes — all mutating methods (`create`, `update`, `delete`, `restore`) |
| **Audit Logging** | Yes — `create_material_model`, `update_material_model`, `delete_material_model`, `restore_material_model` |
| **Event Dispatch** | None |
| **Return Types** | `findAllPaginated(): LengthAwarePaginator`, `findAll(): Collection`, `findById(): ?MaterialModel`, `findByMaterialTypeId(): Collection`, `create(): MaterialModel`, `update(): MaterialModel`, `delete(): void`, `restore(): void` |

---

#### `MaterialService`

| Attribute | Value |
|-----------|-------|
| **Repository Dependencies** | `MaterialRepositoryInterface` |
| **Transaction Usage** | Yes — all mutating methods (`create`, `update`, `delete`, `restore`) |
| **Audit Logging** | Yes — `create_material`, `update_material`, `delete_material`, `restore_material` |
| **Event Dispatch** | None |
| **Return Types** | `findAllPaginated(): LengthAwarePaginator`, `findAll(): Collection`, `findById(): ?Material`, `findByName(): ?Material`, `create(): Material`, `update(): Material`, `delete(): void`, `restore(): void` |

---

#### `BarcodeService`

| Attribute | Value |
|-----------|-------|
| **Repository Dependencies** | `BarcodeRepositoryInterface` |
| **Service Dependencies** | `BarcodeHistoryService`, `AuditLogService`, `ActivityLogService` |
| **Transaction Usage** | Yes — all mutating methods (`create`, `update`, `delete`, `restore`) |
| **Audit Logging** | Yes — `create_barcode`, `update_barcode`, `delete_barcode`, `restore_barcode` |
| **Event Dispatch** | None |
| **Return Types** | `findAllPaginated(): LengthAwarePaginator`, `findAll(): Collection`, `findById(): ?Barcode`, `findByBarcodeId(): ?Barcode`, `generateBarcodeId(): string`, `create(): Barcode`, `update(): Barcode`, `delete(): void`, `restore(): void` |

---

#### `BarcodeHistoryService`

| Attribute | Value |
|-----------|-------|
| **Repository Dependencies** | `BarcodeHistoryRepositoryInterface` |
| **Transaction Usage** | No |
| **Audit Logging** | No (delegates to BarcodeService) |
| **Event Dispatch** | None |
| **Return Types** | `findByBarcodeId(): Collection`, `findAllPaginatedByBarcode(): LengthAwarePaginator`, `create(): BarcodeHistory` |

---

#### `RoleService`

| Attribute | Value |
|-----------|-------|
| **Repository Dependencies** | `RoleRepositoryInterface` |
| **Transaction Usage** | Yes — all mutating methods (`create`, `update`, `delete`, `restore`) |
| **Audit Logging** | Yes — `create_role`, `update_role`, `delete_role`, `restore_role` |
| **Event Dispatch** | None |
| **Return Types** | `findAllPaginated(): LengthAwarePaginator`, `findAll(): Collection`, `findById(): ?Role`, `findByName(): ?Role`, `create(): Role`, `update(): Role`, `delete(): void`, `restore(): void` |

---

#### `PermissionService`

| Attribute | Value |
|-----------|-------|
| **Repository Dependencies** | `PermissionRepositoryInterface` |
| **Transaction Usage** | Yes — all mutating methods (`create`, `update`, `delete`, `restore`) |
| **Audit Logging** | Yes — `create_permission`, `update_permission`, `delete_permission`, `restore_permission` |
| **Event Dispatch** | None |
| **Return Types** | `findAllPaginated(): LengthAwarePaginator`, `findAll(): Collection`, `findById(): ?Permission`, `findByName(): ?Permission`, `findByModule(): Collection`, `create(): Permission`, `update(): Permission`, `delete(): void`, `restore(): void` |

---

#### `AuditLogService`

| Attribute | Value |
|-----------|-------|
| **Repository Dependencies** | `AuditLogRepositoryInterface` |
| **Transaction Usage** | No |
| **Audit Logging** | N/A (self) |
| **Event Dispatch** | None |
| **Return Types** | `log(): void` |

---

#### `ActivityLogService`

| Attribute | Value |
|-----------|-------|
| **Repository Dependencies** | `ActivityLogRepositoryInterface` |
| **Transaction Usage** | No |
| **Audit Logging** | N/A (self) |
| **Event Dispatch** | None |
| **Return Types** | `log(): void` |

---

### Repositories — Detailed Metadata

#### `UserRepository`

| Attribute | Value |
|-----------|-------|
| **Interface** | `UserRepositoryInterface` |
| **Model** | `User` |
| **Supports Filtering** | Yes — `search` (username/email/full_name), `status`, `is_active`, `role`, `trashed` |
| **Supports Pagination** | Yes |
| **Supports Sorting** | Yes — `created_at desc` (default) |
| **Query Complexity** | Moderate (multiple filter conditions, whereHas for role) |

---

#### `SiteRepository`

| Attribute | Value |
|-----------|-------|
| **Interface** | `SiteRepositoryInterface` |
| **Model** | `Site` |
| **Supports Filtering** | Yes — `search` (site_id/site_name), `site_id`, `site_name`, `is_active`, `trashed` |
| **Supports Pagination** | Yes |
| **Supports Sorting** | Yes — `created_at desc` (default) |
| **Query Complexity** | Simple |

---

#### `MaterialTypeRepository`

| Attribute | Value |
|-----------|-------|
| **Interface** | `MaterialTypeRepositoryInterface` |
| **Model** | `MaterialType` |
| **Supports Filtering** | Yes — `search` (name), `is_active`, `trashed` |
| **Supports Pagination** | Yes |
| **Supports Sorting** | Yes — `created_at desc` (default) |
| **Query Complexity** | Simple |

---

#### `MaterialModelRepository`

| Attribute | Value |
|-----------|-------|
| **Interface** | `MaterialModelRepositoryInterface` |
| **Model** | `MaterialModel` |
| **Supports Filtering** | Yes — `search` (name), `material_type_id`, `is_active`, `trashed` |
| **Supports Pagination** | Yes |
| **Supports Sorting** | Yes — `created_at desc` (default) |
| **Query Complexity** | Simple |
| **Eager Loads** | `materialType` |

---

#### `MaterialRepository`

| Attribute | Value |
|-----------|-------|
| **Interface** | `MaterialRepositoryInterface` |
| **Model** | `Material` |
| **Supports Filtering** | Yes — `search` (name/material_code), `material_type_id`, `material_model_id`, `is_active`, `trashed` |
| **Supports Pagination** | Yes |
| **Supports Sorting** | Yes — `created_at desc` (default) |
| **Query Complexity** | Simple |
| **Eager Loads** | `materialType`, `materialModel` |

---

#### `BarcodeRepository`

| Attribute | Value |
|-----------|-------|
| **Interface** | `BarcodeRepositoryInterface` |
| **Model** | `Barcode` |
| **Supports Filtering** | Yes — `search` (barcode_id/serial_number), `barcode_id`, `serial_number`, `site_id`, `material_id`, `status`, `is_active`, `date_from`, `date_to`, `trashed` |
| **Supports Pagination** | Yes |
| **Supports Sorting** | Yes — `created_at desc` (default) |
| **Query Complexity** | Moderate (multiple filter conditions, date range) |
| **Eager Loads** | `material.materialType`, `material.materialModel`, `site`, `createdBy`, `updatedBy` |

---

#### `BarcodeHistoryRepository`

| Attribute | Value |
|-----------|-------|
| **Interface** | `BarcodeHistoryRepositoryInterface` |
| **Model** | `BarcodeHistory` |
| **Supports Filtering** | Yes — `field_name`, `changed_by`, `date_from`, `date_to` |
| **Supports Pagination** | Yes |
| **Supports Sorting** | Yes — `created_at desc` (default) |
| **Query Complexity** | Simple |
| **Eager Loads** | `changedBy` |

---

#### `RoleRepository`

| Attribute | Value |
|-----------|-------|
| **Interface** | `RoleRepositoryInterface` |
| **Model** | `Role` |
| **Supports Filtering** | Yes — `search` (name/display_name), `is_active`, `trashed` |
| **Supports Pagination** | Yes |
| **Supports Sorting** | Yes — `created_at desc` (default) |
| **Query Complexity** | Simple |

---

#### `PermissionRepository`

| Attribute | Value |
|-----------|-------|
| **Interface** | `PermissionRepositoryInterface` |
| **Model** | `Permission` |
| **Supports Filtering** | Yes — `search` (name/display_name), `module`, `is_active`, `trashed` |
| **Supports Pagination** | Yes |
| **Supports Sorting** | Yes — `module`, `name` (default) |
| **Query Complexity** | Simple |

---

#### `AuditLogRepository`

| Attribute | Value |
|-----------|-------|
| **Interface** | `AuditLogRepositoryInterface` |
| **Model** | `AuditLog` |
| **Supports Filtering** | No |
| **Supports Pagination** | No |
| **Supports Sorting** | No |
| **Query Complexity** | Simple (single `create` method) |

---

#### `ActivityLogRepository`

| Attribute | Value |
|-----------|-------|
| **Interface** | `ActivityLogRepositoryInterface` |
| **Model** | `ActivityLog` |
| **Supports Filtering** | No |
| **Supports Pagination** | No |
| **Supports Sorting** | No |
| **Query Complexity** | Simple (single `create` method) |

---

### Models — Detailed Metadata

#### `User`

| Attribute | Value |
|-----------|-------|
| **Fillable** | `username`, `email`, `password`, `full_name`, `status`, `is_active`, `email_verified_at`, `last_login_at`, `password_changed_at` |
| **Casts** | `is_active` → `boolean`, `email_verified_at` → `datetime`, `last_login_at` → `datetime`, `password_changed_at` → `datetime` |
| **Relationships** | `roles(): BelongsToMany`, `permissions(): BelongsToMany` (scoped), `auditLogs(): HasMany`, `activityLogs(): HasMany` |
| **Scopes** | None |
| **Uses SoftDeletes** | Yes |
| **Factory** | Unknown |
| **Observer** | Unknown |

---

#### `Role`

| Attribute | Value |
|-----------|-------|
| **Fillable** | `name`, `display_name`, `description`, `is_active` |
| **Casts** | `is_active` → `boolean` |
| **Relationships** | `users(): BelongsToMany`, `permissions(): BelongsToMany` |
| **Scopes** | None |
| **Uses SoftDeletes** | Yes |
| **Factory** | Unknown |
| **Observer** | Unknown |

---

#### `Permission`

| Attribute | Value |
|-----------|-------|
| **Fillable** | `name`, `display_name`, `module`, `description`, `is_active` |
| **Casts** | `is_active` → `boolean` |
| **Relationships** | `roles(): BelongsToMany` |
| **Scopes** | None |
| **Uses SoftDeletes** | Yes |
| **Factory** | Unknown |
| **Observer** | Unknown |

---

#### `Site`

| Attribute | Value |
|-----------|-------|
| **Fillable** | `site_id`, `site_name`, `region`, `address`, `latitude`, `longitude`, `is_active` |
| **Casts** | `is_active` → `boolean` |
| **Relationships** | `barcodes(): HasMany` |
| **Scopes** | None |
| **Uses SoftDeletes** | Yes |
| **Factory** | Unknown |
| **Observer** | Unknown |

---

#### `MaterialType`

| Attribute | Value |
|-----------|-------|
| **Fillable** | `name`, `description`, `is_active` |
| **Casts** | `is_active` → `boolean` |
| **Relationships** | `materials(): HasMany`, `materialModels(): HasMany` |
| **Scopes** | None |
| **Uses SoftDeletes** | Yes |
| **Factory** | Unknown |
| **Observer** | Unknown |

---

#### `MaterialModel`

| Attribute | Value |
|-----------|-------|
| **Fillable** | `material_type_id`, `name`, `description`, `is_active` |
| **Casts** | `is_active` → `boolean` |
| **Relationships** | `materialType(): BelongsTo`, `materials(): HasMany` |
| **Scopes** | None |
| **Uses SoftDeletes** | Yes |
| **Factory** | Unknown |
| **Observer** | Unknown |

---

#### `Material`

| Attribute | Value |
|-----------|-------|
| **Fillable** | `material_type_id`, `material_model_id`, `material_code`, `name`, `description`, `is_active` |
| **Casts** | `is_active` → `boolean` |
| **Relationships** | `materialType(): BelongsTo`, `materialModel(): BelongsTo`, `barcodes(): HasMany` |
| **Scopes** | None |
| **Uses SoftDeletes** | Yes |
| **Factory** | Unknown |
| **Observer** | Unknown |

---

#### `Barcode`

| Attribute | Value |
|-----------|-------|
| **Fillable** | `barcode_id`, `material_id`, `site_id`, `serial_number`, `status`, `description`, `is_active`, `created_by`, `updated_by` |
| **Casts** | `is_active` → `boolean` |
| **Relationships** | `material(): BelongsTo`, `site(): BelongsTo`, `createdBy(): BelongsTo` (User), `updatedBy(): BelongsTo` (User), `histories(): HasMany` |
| **Scopes** | None |
| **Uses SoftDeletes** | Yes |
| **Factory** | Unknown |
| **Observer** | Unknown |

---

#### `BarcodeHistory`

| Attribute | Value |
|-----------|-------|
| **Fillable** | `barcode_id`, `field_name`, `old_value`, `new_value`, `changed_by`, `change_reason` |
| **Casts** | None |
| **Relationships** | `barcode(): BelongsTo`, `changedBy(): BelongsTo` (User) |
| **Scopes** | None |
| **Uses SoftDeletes** | No |
| **Factory** | Unknown |
| **Observer** | Unknown |

---

#### `AuditLog`

| Attribute | Value |
|-----------|-------|
| **Fillable** | `user_id`, `entity_type`, `entity_id`, `action`, `old_values`, `new_values`, `ip_address`, `user_agent` |
| **Casts** | `old_values` → `array`, `new_values` → `array` |
| **Relationships** | `user(): BelongsTo` |
| **Scopes** | None |
| **Uses SoftDeletes** | No |
| **Factory** | Unknown |
| **Observer** | Unknown |
| **Timestamps** | `$timestamps = false`, `UPDATED_AT = null` |

---

#### `ActivityLog`

| Attribute | Value |
|-----------|-------|
| **Fillable** | `user_id`, `activity`, `module`, `description`, `ip_address`, `user_agent`, `session_id` |
| **Casts** | None |
| **Relationships** | `user(): BelongsTo` |
| **Scopes** | None |
| **Uses SoftDeletes** | No |
| **Factory** | Unknown |
| **Observer** | Unknown |
| **Timestamps** | `$timestamps = false`, `UPDATED_AT = null` |

---

## Architecture Coverage

| Layer | Total Files | Key Patterns |
|-------|-------------|--------------|
| **Enums** | 4 | Backed enums with `label()` and `module()` methods |
| **Models** | 10 | Eloquent ORM, 8 use `SoftDeletes`, 2 are immutable logs |
| **Interfaces** | 11 | Repository contracts with CRUD + soft-delete methods |
| **Repositories** | 11 | Query builder pattern, filter/pagination/sort support |
| **DTOs** | 10 | Immutable readonly classes with `fromRequest()` factory |
| **Services** | 12 | Business logic layer, all mutations wrapped in `DB::transaction()` |
| **Controllers** | 9 | Thin controllers delegating to services, consistent JSON response format |
| **Form Requests** | 19 | Validation with `unique` ignore-on-update, `exists` rules |
| **API Resources** | 10 | Transformers with nested relation loading |
| **Middleware** | 1 | Permission-based route protection via Sanctum |
| **Providers** | 1 | Dependency injection bindings (Interface → Implementation) |
| **Routes** | 1 file | 9 resource groups, all protected by `auth:sanctum` + `permission:*` |

**Cross-Cutting Concerns:**
- **Audit Trail**: Every mutation logs to both `AuditLog` (structured before/after) and `ActivityLog` (human-readable)
- **Soft Deletes**: 8 of 10 models support soft deletes with `restore` endpoints
- **Transaction Safety**: All write operations use `DB::transaction()` for atomicity
- **No Events**: No event dispatchers or listeners are used in the current codebase
- **No Factories/Observers**: No model factories or observers are defined in the current codebase

---

## Key Architecture Decisions


1. **Repository Pattern** — All database queries go through Repository classes, bound via interfaces in `AppServiceProvider`
2. **DTO Pattern** — Data transfer between layers uses immutable readonly DTOs
3. **Service Layer** — All business logic lives in Service classes, not in Controllers or Models
4. **Soft Deletes** — All major entities use Laravel's `SoftDeletes` trait
5. **Audit Trail** — Every mutation is logged to both `audit_logs` (structured before/after) and `activity_logs` (human-readable)
6. **Permission-based Auth** — Route access is controlled via `CheckPermission` middleware using Sanctum tokens
7. **Barcode ID Format** — `BRC-YYYYMMDD-NNN` with daily sequence reset
8. **Field-level History** — Barcode updates track individual field changes with old/new values


