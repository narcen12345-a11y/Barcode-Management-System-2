# Eloquent Model Layer Audit Report

> **Generated:** 2026-07-15  
> **Scope:** All 11 Eloquent Model Classes  
> **Methodology:** Manual code inspection against 20-point checklist  
> **Status:** ⚠️ Issues Found

---

## Audit Checklist Summary

| # | Check | Status |
|---|-------|--------|
| 1 | Fillable / guarded consistency | ✅ All correct |
| 2 | Cast definitions | ⚠️ 2 issues |
| 3 | Relationship correctness | ✅ All correct |
| 4 | Relationship naming | ⚠️ 1 issue |
| 5 | Eager loading opportunities | ⚠️ 1 issue |
| 6 | Global scopes | ✅ N/A |
| 7 | Local scopes | ⚠️ 1 issue |
| 8 | SoftDeletes usage | ✅ All correct |
| 9 | Hidden / visible attributes | ✅ All correct |
| 10 | Accessors / mutators | ⚠️ 1 issue |
| 11 | Enum casts | ⚠️ 1 issue |
| 12 | Factory availability | ⚠️ 1 issue |
| 13 | Observer usage | ⚠️ 1 issue |
| 14 | Mass assignment risks | ✅ All correct |
| 15 | N+1 query risks | ⚠️ 1 issue |
| 16 | Circular relationship risks | ✅ None found |
| 17 | Model events | ⚠️ 1 issue |
| 18 | Business logic leakage | ⚠️ 1 issue |
| 19 | Laravel best practices | ⚠️ 2 issues |
| 20 | Performance considerations | ⚠️ 1 issue |

---

## Model-by-Model Audit

---

### 1. User

**File:** `backend/app/Models/User.php`  
**Lines:** 113  
**Traits:** `HasApiTokens`, `Notifiable`, `SoftDeletes`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| USR-MOD-1 | **Medium** | Missing Enum Cast | `status` is stored as a string but has no cast to `UserStatusEnum`. The model's `casts()` method does not include `'status' => UserStatusEnum::class`. | The `status` attribute is returned as a raw string from the model. Enum methods like `label()` are not available without manual conversion. | Add `'status' => UserStatusEnum::class` to the `casts()` method. |
| USR-MOD-2 | **Medium** | Business Logic Leakage | `hasPermission()`, `hasAnyPermission()`, `hasRole()`, `isSuperAdmin()`, `isAdmin()`, `canLogin()`, `markAsLoggedIn()`, `setPassword()` — 8 business logic methods in the model. | Business logic should be in the Service layer, not the Model. Models should only define relationships, casts, and scopes. | Move permission/role check methods to a dedicated service or policy class. Keep only `canLogin()` if used for authentication guard. |
| USR-MOD-3 | **Low** | N+1 Query Risk in `permissions()` | The `permissions()` relationship (line 51-55) uses `$this->roles()->pluck('roles.id')` inside the relationship definition. This executes a query every time the relationship is accessed. | Every call to `$user->permissions` triggers an additional query to get role IDs, then another to get permissions. This cannot be eager loaded. | Use `hasManyThrough` or a different approach to define permissions through roles. |
| USR-MOD-4 | **Low** | Missing Local Scopes | No local scopes for common queries like `active()`, `verified()`, `byRole()`. | Controllers/services must repeat `where('is_active', true)` or `where('status', 'active')` manually. | Add scopes: `scopeActive()`, `scopeVerified()`, `scopeByRole()`. |
| USR-MOD-5 | **Low** | Missing Factory | No `UserFactory` defined. | Cannot use `User::factory()` for seeding or testing. | Create `database/factories/UserFactory.php`. |
| USR-MOD-6 | **Low** | Missing Observer | No `UserObserver` for events like `creating` (hash password), `created` (assign default role), etc. | Password hashing is done manually in `setPassword()` method. If a user is created via `User::create()`, the password won't be hashed. | Create a `UserObserver` with a `creating` event that hashes the password automatically. |

**No issues found for:** Fillable/guarded, Relationship correctness, Relationship naming, Global scopes, SoftDeletes, Hidden/visible, Mass assignment risks, Circular relationships, Laravel best practices (partial).

---

### 2. Site

**File:** `backend/app/Models/Site.php`  
**Lines:** 34  
**Traits:** `SoftDeletes`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| SITE-MOD-1 | **Low** | Missing Local Scopes | No `scopeActive()` for filtering active sites. | Services must repeat `where('is_active', true)`. | Add `scopeActive()`. |
| SITE-MOD-2 | **Low** | Missing Factory | No `SiteFactory`. | Cannot use `Site::factory()` for seeding. | Create `database/factories/SiteFactory.php`. |

**No issues found for:** Fillable/guarded, Cast definitions, Relationship correctness, Relationship naming, Eager loading, Global scopes, SoftDeletes, Hidden/visible, Accessors/mutators, Enum casts, Observer, Mass assignment, N+1 risks, Circular relationships, Model events, Business logic leakage, Laravel best practices, Performance.

---

### 3. MaterialType

**File:** `backend/app/Models/MaterialType.php`  
**Lines:** 35  
**Traits:** `SoftDeletes`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| MT-MOD-1 | **Low** | Missing Local Scopes | No `scopeActive()`. | Services must repeat `where('is_active', true)`. | Add `scopeActive()`. |
| MT-MOD-2 | **Low** | Missing Factory | No `MaterialTypeFactory`. | Cannot use `MaterialType::factory()`. | Create factory. |

**No issues found for:** Fillable/guarded, Cast definitions, Relationship correctness, Relationship naming, Eager loading, Global scopes, SoftDeletes, Hidden/visible, Accessors/mutators, Enum casts, Observer, Mass assignment, N+1 risks, Circular relationships, Model events, Business logic leakage, Laravel best practices, Performance.

---

### 4. MaterialModel

**File:** `backend/app/Models/MaterialModel.php`  
**Lines:** 37  
**Traits:** `SoftDeletes`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| MM-MOD-1 | **Low** | Missing Local Scopes | No `scopeActive()`. | Services must repeat `where('is_active', true)`. | Add `scopeActive()`. |
| MM-MOD-2 | **Low** | Missing Factory | No `MaterialModelFactory`. | Cannot use `MaterialModel::factory()`. | Create factory. |

**No issues found for:** Fillable/guarded, Cast definitions, Relationship correctness, Relationship naming, Eager loading, Global scopes, SoftDeletes, Hidden/visible, Accessors/mutators, Enum casts, Observer, Mass assignment, N+1 risks, Circular relationships, Model events, Business logic leakage, Laravel best practices, Performance.

---

### 5. Material

**File:** `backend/app/Models/Material.php`  
**Lines:** 44  
**Traits:** `SoftDeletes`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| MAT-MOD-1 | **Low** | Missing Local Scopes | No `scopeActive()`. | Services must repeat `where('is_active', true)`. | Add `scopeActive()`. |
| MAT-MOD-2 | **Low** | Missing Factory | No `MaterialFactory`. | Cannot use `Material::factory()`. | Create factory. |

**No issues found for:** Fillable/guarded, Cast definitions, Relationship correctness, Relationship naming, Eager loading, Global scopes, SoftDeletes, Hidden/visible, Accessors/mutators, Enum casts, Observer, Mass assignment, N+1 risks, Circular relationships, Model events, Business logic leakage, Laravel best practices, Performance.

---

### 6. Barcode

**File:** `backend/app/Models/Barcode.php`  
**Lines:** 57  
**Traits:** `SoftDeletes`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| BRC-MOD-1 | **Medium** | Missing Enum Cast | `status` is stored as a string but has no cast to `BarcodeStatusEnum`. The model's `casts()` method does not include `'status' => BarcodeStatusEnum::class`. | The `status` attribute is returned as a raw string. Enum methods are not available without manual conversion. | Add `'status' => BarcodeStatusEnum::class` to the `casts()` method. |
| BRC-MOD-2 | **Low** | Missing Local Scopes | No `scopeActive()`, `scopeByStatus()`, `scopeBySite()`, `scopeByMaterial()`. | Services must repeat common filters manually. | Add scopes for common filters. |
| BRC-MOD-3 | **Low** | Missing Factory | No `BarcodeFactory`. | Cannot use `Barcode::factory()`. | Create factory. |
| BRC-MOD-4 | **Low** | Missing Observer | No `BarcodeObserver` for events like `creating` (generate barcode_id), `updated` (log history), etc. | Barcode history logging is done manually in the service layer. An observer could automate this. | Consider creating a `BarcodeObserver` for cross-cutting concerns. |

**No issues found for:** Fillable/guarded, Relationship correctness, Relationship naming, Eager loading, Global scopes, SoftDeletes, Hidden/visible, Accessors/mutators, Mass assignment, N+1 risks, Circular relationships, Business logic leakage, Laravel best practices, Performance.

---

### 7. BarcodeHistory

**File:** `backend/app/Models/BarcodeHistory.php`  
**Lines:** 28  
**Traits:** None

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| BRC-HIST-MOD-1 | **Low** | Missing SoftDeletes | `BarcodeHistory` does not use `SoftDeletes`. | History records cannot be soft-deleted. If a barcode is deleted, its history is either cascade-deleted or orphaned. | Consider adding `SoftDeletes` if history retention is required. |
| BRC-HIST-MOD-2 | **Low** | Missing Factory | No `BarcodeHistoryFactory`. | Cannot use `BarcodeHistory::factory()`. | Create factory. |
| BRC-HIST-MOD-3 | **Low** | Missing Local Scopes | No scopes for filtering by field, barcode, or user. | Services must repeat filters manually. | Add scopes. |

**No issues found for:** Fillable/guarded, Cast definitions, Relationship correctness, Relationship naming, Eager loading, Global scopes, Hidden/visible, Accessors/mutators, Enum casts, Observer, Mass assignment, N+1 risks, Circular relationships, Model events, Business logic leakage, Laravel best practices, Performance.

---

### 8. AuditLog

**File:** `backend/app/Models/AuditLog.php`  
**Lines:** 37  
**Traits:** None

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| AUDIT-MOD-1 | **Low** | Custom Timestamp Handling | `$timestamps = false` and `const UPDATED_AT = null` are both used to disable `updated_at`. Only one is needed. | Redundant code — both achieve the same result. | Remove `const UPDATED_AT = null` and keep `$timestamps = false`. |
| AUDIT-MOD-2 | **Low** | Missing SoftDeletes | Audit logs cannot be soft-deleted. | If an audit log needs to be "hidden" for privacy reasons, it must be hard-deleted. | Consider adding `SoftDeletes` if needed. |
| AUDIT-MOD-3 | **Low** | Missing Factory | No `AuditLogFactory`. | Cannot use `AuditLog::factory()`. | Create factory. |

**No issues found for:** Fillable/guarded, Cast definitions, Relationship correctness, Relationship naming, Eager loading, Global scopes, Hidden/visible, Accessors/mutators, Enum casts, Observer, Mass assignment, N+1 risks, Circular relationships, Model events, Business logic leakage, Laravel best practices, Performance.

---

### 9. ActivityLog

**File:** `backend/app/Models/ActivityLog.php`  
**Lines:** 28  
**Traits:** None

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| ACT-MOD-1 | **Low** | Custom Timestamp Handling | Same as AUDIT-MOD-1 — both `$timestamps = false` and `const UPDATED_AT = null` are used. | Redundant code. | Remove `const UPDATED_AT = null`. |
| ACT-MOD-2 | **Low** | Missing SoftDeletes | Activity logs cannot be soft-deleted. | Same as AUDIT-MOD-2. | Consider adding `SoftDeletes` if needed. |
| ACT-MOD-3 | **Low** | Missing Factory | No `ActivityLogFactory`. | Cannot use `ActivityLog::factory()`. | Create factory. |

**No issues found for:** Fillable/guarded, Cast definitions, Relationship correctness, Relationship naming, Eager loading, Global scopes, Hidden/visible, Accessors/mutators, Enum casts, Observer, Mass assignment, N+1 risks, Circular relationships, Model events, Business logic leakage, Laravel best practices, Performance.

---

### 10. Role

**File:** `backend/app/Models/Role.php`  
**Lines:** 48  
**Traits:** `SoftDeletes`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| ROL-MOD-1 | **Low** | Business Logic Leakage | `hasPermission()` and `syncPermissions()` are business logic methods in the model. | Business logic should be in the Service layer. | Move to `RoleService`. |
| ROL-MOD-2 | **Low** | Missing Local Scopes | No `scopeActive()`. | Services must repeat `where('is_active', true)`. | Add `scopeActive()`. |
| ROL-MOD-3 | **Low** | Missing Factory | No `RoleFactory`. | Cannot use `Role::factory()`. | Create factory. |

**No issues found for:** Fillable/guarded, Cast definitions, Relationship correctness, Relationship naming, Eager loading, Global scopes, SoftDeletes, Hidden/visible, Accessors/mutators, Enum casts, Observer, Mass assignment, N+1 risks, Circular relationships, Model events, Laravel best practices, Performance.

---

### 11. Permission

**File:** `backend/app/Models/Permission.php`  
**Lines:** 33  
**Traits:** `SoftDeletes`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| PERM-MOD-1 | **Low** | Missing Local Scopes | No `scopeActive()`, `scopeByModule()`. | Services must repeat filters manually. | Add scopes. |
| PERM-MOD-2 | **Low** | Missing Factory | No `PermissionFactory`. | Cannot use `Permission::factory()`. | Create factory. |

**No issues found for:** Fillable/guarded, Cast definitions, Relationship correctness, Relationship naming, Eager loading, Global scopes, SoftDeletes, Hidden/visible, Accessors/mutators, Enum casts, Observer, Mass assignment, N+1 risks, Circular relationships, Model events, Business logic leakage, Laravel best practices, Performance.

---

## Cross-Cutting Issues

### Critical Issues

**None found.**

### High Priority Issues

| # | Severity | Category | Models Affected | Problem | Suggested Fix |
|---|----------|----------|-----------------|---------|---------------|
| H-1 | **High** | Missing Enum Casts | `User`, `Barcode` | Two models have enum fields (`status`) that are not cast to their respective Enum classes (`UserStatusEnum`, `BarcodeStatusEnum`). This means: (1) Enum methods like `label()` are not available, (2) The attribute is always a raw string, (3) Validation at the model level is bypassed. | Add `'status' => UserStatusEnum::class` to `User::casts()` and `'status' => BarcodeStatusEnum::class` to `Barcode::casts()`. |

### Medium Issues

| # | Severity | Category | Models Affected | Problem | Suggested Fix |
|---|----------|----------|-----------------|---------|---------------|
| M-1 | **Medium** | Business Logic Leakage | `User`, `Role` | `User` has 8 business logic methods (`hasPermission`, `hasAnyPermission`, `hasRole`, `isSuperAdmin`, `isAdmin`, `canLogin`, `markAsLoggedIn`, `setPassword`). `Role` has 2 (`hasPermission`, `syncPermissions`). Models should only define relationships, casts, and scopes. | Move business logic to Service layer or Policy classes. |
| M-2 | **Medium** | N+1 Query Risk | `User` | The `permissions()` relationship (line 51-55) uses `$this->roles()->pluck('roles.id')` inside the relationship definition. This cannot be eager loaded and executes a subquery every time. | Refactor to use `hasManyThrough` or a dedicated permission check service. |

### Low Issues

| # | Severity | Category | Models Affected | Problem | Suggested Fix |
|---|----------|----------|-----------------|---------|---------------|
| L-1 | **Low** | Missing Local Scopes | All 11 models | No models define local scopes for common filters like `active()`, `byStatus()`, `byModule()`, etc. | Add `scopeActive()` to all models with `is_active` field. Add domain-specific scopes as needed. |
| L-2 | **Low** | Missing Factories | All 11 models | No model has a corresponding factory class. | Create factories in `database/factories/`. |
| L-3 | **Low** | Missing Observers | `User`, `Barcode` | No observers for cross-cutting concerns like password hashing (User) or history logging (Barcode). | Create observers for automated event handling. |
| L-4 | **Low** | Redundant Timestamp Code | `AuditLog`, `ActivityLog` | Both models use `$timestamps = false` AND `const UPDATED_AT = null` — only one is needed. | Remove `const UPDATED_AT = null`. |
| L-5 | **Low** | Missing SoftDeletes | `BarcodeHistory`, `AuditLog`, `ActivityLog` | History and log models do not use `SoftDeletes`. | Consider adding if soft-deletion is required. |

---

## Summary

| Metric | Count |
|--------|-------|
| **Critical Issues** | 0 |
| **High Priority Issues** | 1 |
| **Medium Issues** | 2 |
| **Low Issues** | 5 |
| **Total Issues** | 8 |
| **Models with No Issues** | 0 (all have at least low-severity findings) |

### Key Takeaways

1. **Missing Enum Casts (High)**: `User` and `Barcode` models have `status` fields that are not cast to their respective Enum classes (`UserStatusEnum`, `BarcodeStatusEnum`). This means enum methods are unavailable and the attribute is always a raw string.

2. **Business Logic Leakage (Medium)**: `User` model contains 8 business logic methods (`hasPermission`, `hasRole`, `isSuperAdmin`, etc.) and `Role` contains 2 (`hasPermission`, `syncPermissions`). Models should be "dumb" — only defining relationships, casts, and scopes.

3. **N+1 Query Risk in User::permissions() (Medium)**: The `permissions()` relationship on `User` uses `$this->roles()->pluck('roles.id')` which cannot be eager loaded and executes a subquery every time.

4. **Overall Quality**: The Model layer is generally well-structured:
   - ✅ **Fillable/guarded** — all models use `$fillable` (no `$guarded`), preventing mass assignment vulnerabilities
   - ✅ **SoftDeletes** — 9 out of 11 models use `SoftDeletes` (correct for most entities)
   - ✅ **Relationship correctness** — all relationships use correct types (`BelongsTo`, `HasMany`, `BelongsToMany`)
   - ✅ **Relationship naming** — consistent naming (singular for `BelongsTo`, plural for `HasMany`/`BelongsToMany`)
   - ✅ **Hidden attributes** — `User` correctly hides `password` and `remember_token`
   - ✅ **Cast definitions** — all boolean fields are cast correctly
   - ✅ **No global scopes** — no hidden query modifications
   - ✅ **No circular relationships** — all relationship chains are acyclic
   - ✅ **No accessors/mutators** — no hidden transformations (except missing enum casts)

### Model Relationship Map

```
User
  ├── roles() → BelongsToMany (Role, pivot: role_user)
  │     └── permissions() → BelongsToMany (Permission, pivot: permission_role)
  ├── permissions() → BelongsToMany (via roles — N+1 risk)
  ├── auditLogs() → HasMany (AuditLog)
  └── activityLogs() → HasMany (ActivityLog)

Role
  ├── users() → BelongsToMany (User, pivot: role_user)
  └── permissions() → BelongsToMany (Permission, pivot: permission_role)

Permission
  └── roles() → BelongsToMany (Role, pivot: permission_role)

Site
  └── barcodes() → HasMany (Barcode)

MaterialType
  ├── materials() → HasMany (Material)
  └── materialModels() → HasMany (MaterialModel)

MaterialModel
  ├── materialType() → BelongsTo (MaterialType)
  └── materials() → HasMany (Material)

Material
  ├── materialType() → BelongsTo (MaterialType)
  ├── materialModel() → BelongsTo (MaterialModel)
  └── barcodes() → HasMany (Barcode)

Barcode
  ├── material() → BelongsTo (Material)
  ├── site() → BelongsTo (Site)
  ├── createdBy() → BelongsTo (User, foreign: created_by)
  ├── updatedBy() → BelongsTo (User, foreign: updated_by)
  └── histories() → HasMany (BarcodeHistory)

BarcodeHistory
  ├── barcode() → BelongsTo (Barcode)
  └── changedBy() → BelongsTo (User, foreign: changed_by)

AuditLog
  └── user() → BelongsTo (User)

ActivityLog
  └── user() → BelongsTo (User)
```
