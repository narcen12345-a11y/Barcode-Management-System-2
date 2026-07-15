# DTO Layer Audit Report

> **Generated:** 2026-07-15  
> **Scope:** All 10 DTO Classes  
> **Methodology:** Manual code inspection against 20-point checklist  
> **Status:** ⚠️ Issues Found

---

## Audit Checklist Summary

| # | Check | Status |
|---|-------|--------|
| 1 | Immutable implementation | ✅ All correct |
| 2 | `readonly` usage | ✅ All correct |
| 3 | Factory methods | ✅ All correct |
| 4 | `fromRequest` consistency | ⚠️ 2 issues |
| 5 | Type declarations | ⚠️ 1 issue |
| 6 | Nullable handling | ✅ All correct |
| 7 | Default values | ✅ All correct |
| 8 | Enum usage | ⚠️ 1 issue |
| 9 | Date handling | ✅ N/A (no dates in DTOs) |
| 10 | Value Object opportunities | ⚠️ 1 issue |
| 11 | Mapping consistency | ⚠️ 2 issues |
| 12 | Missing fields | ⚠️ 1 issue |
| 13 | Unused properties | ✅ None found |
| 14 | Duplicate DTO | ✅ None found |
| 15 | Validation duplication | ⚠️ 1 issue |
| 16 | Serialization | ⚠️ 1 issue |
| 17 | Naming consistency | ⚠️ 1 issue |
| 18 | Constructor complexity | ✅ All correct |
| 19 | SOLID | ✅ All correct |
| 20 | Laravel best practice | ⚠️ 2 issues |

---

## DTO-by-DTO Audit

---

### 1. SiteDTO

**File:** `backend/app/DTOs/SiteDTO.php`  
**Lines:** 42  
**Used by:** `SiteService::create()`

#### Issues Found

| # | Severity | Category | Problem | Impact | Suggested Fix |
|---|----------|----------|---------|--------|---------------|
| SITE-DTO-1 | **Low** | Missing `toArray()` for null values | `toArray()` returns `'region' => $this->region` which could be `null`. The database column is nullable, so this is technically correct, but the array will contain explicit `null` values for all optional fields. | Minor — when used for creation, null values are passed explicitly to the database. | No fix needed — explicit nulls are fine for Eloquent `create()`. |
| SITE-DTO-2 | **Low** | No Update DTO | There is no `UpdateSiteDTO`. The `SiteService::update()` accepts raw `array $data` instead. | Inconsistent with create pattern. | Create `UpdateSiteDTO` with all-nullable properties. |

**No issues found for:** Immutability, `readonly`, factory method, type declarations, nullable handling, default values, enum usage, value objects, mapping, missing fields, unused properties, duplicate, validation duplication, serialization, naming, constructor complexity, SOLID, Laravel best practices.

---

### 2. MaterialTypeDTO

**File:** `backend/app/DTOs/MaterialTypeDTO.php`  
**Lines:** 30  
**Used by:** `MaterialTypeService::create()`

#### Issues Found

| # | Severity | Category | Problem | Impact | Suggested Fix |
|---|----------|----------|---------|--------|---------------|
| MT-DTO-1 | **Low** | No Update DTO | There is no `UpdateMaterialTypeDTO`. The `MaterialTypeService::update()` accepts raw `array $data`. | Inconsistent with create pattern. | Create `UpdateMaterialTypeDTO` with all-nullable properties. |

**No issues found for:** Immutability, `readonly`, factory method, type declarations, nullable handling, default values, enum usage, value objects, mapping, missing fields, unused properties, duplicate, validation duplication, serialization, naming, constructor complexity, SOLID, Laravel best practices.

---

### 3. MaterialModelDTO

**File:** `backend/app/DTOs/MaterialModelDTO.php`  
**Lines:** 33  
**Used by:** `MaterialModelService::create()`

#### Issues Found

| # | Severity | Category | Problem | Impact | Suggested Fix |
|---|----------|----------|---------|--------|---------------|
| MM-DTO-1 | **Low** | No Update DTO | There is no `UpdateMaterialModelDTO`. The `MaterialModelService::update()` accepts raw `array $data`. | Inconsistent with create pattern. | Create `UpdateMaterialModelDTO` with all-nullable properties. |

**No issues found for:** Immutability, `readonly`, factory method, type declarations, nullable handling, default values, enum usage, value objects, mapping, missing fields, unused properties, duplicate, validation duplication, serialization, naming, constructor complexity, SOLID, Laravel best practices.

---

### 4. MaterialDTO

**File:** `backend/app/DTOs/MaterialDTO.php`  
**Lines:** 39  
**Used by:** `MaterialService::create()`

#### Issues Found

| # | Severity | Category | Problem | Impact | Suggested Fix |
|---|----------|----------|---------|--------|---------------|
| MAT-DTO-1 | **Low** | No Update DTO | There is no `UpdateMaterialDTO`. The `MaterialService::update()` accepts raw `array $data`. | Inconsistent with create pattern. | Create `UpdateMaterialDTO` with all-nullable properties. |

**No issues found for:** Immutability, `readonly`, factory method, type declarations, nullable handling, default values, enum usage, value objects, mapping, missing fields, unused properties, duplicate, validation duplication, serialization, naming, constructor complexity, SOLID, Laravel best practices.

---

### 5. BarcodeDTO

**File:** `backend/app/DTOs/BarcodeDTO.php`  
**Lines:** 43  
**Used by:** `BarcodeService::create()`

#### Issues Found

| # | Severity | Category | Problem | Impact | Suggested Fix |
|---|----------|----------|---------|--------|---------------|
| BRC-DTO-1 | **Medium** | Missing Enum for `status` | The `status` property is typed as `string` but should use `BarcodeStatusEnum`. The `fromRequest()` passes `$data['status']` as a raw string. | Type safety — any string can be passed. If the enum values change, this DTO won't catch it. | Change type to `BarcodeStatusEnum` and cast in `fromRequest()`: `status: BarcodeStatusEnum::from($data['status'])`. |
| BRC-DTO-2 | **Low** | Hardcoded `is_active` | `toArray()` hardcodes `'is_active' => true` (line 39). This assumes all created barcodes are active. | If the business rule changes (e.g., barcodes need approval), this must be updated here. | Add `isActive` as a constructor parameter with default `true`. |
| BRC-DTO-3 | **Low** | Missing `updated_by` in `toArray()` | `toArray()` includes `created_by` but not `updated_by`. The `BarcodeService::create()` sets `created_by` via the DTO, but `updated_by` is not set on creation. | Minor — `updated_by` could be set to the same user on creation. | Add `'updated_by' => $this->createdBy` to `toArray()`. |

**No issues found for:** Immutability, `readonly`, factory method, type declarations (except status), nullable handling, default values, value objects, mapping, missing fields, unused properties, duplicate, validation duplication, serialization, naming, constructor complexity, SOLID, Laravel best practices.

---

### 6. UpdateBarcodeDTO

**File:** `backend/app/DTOs/UpdateBarcodeDTO.php`  
**Lines:** 53  
**Used by:** `BarcodeService::update()`

#### Issues Found

| # | Severity | Category | Problem | Impact | Suggested Fix |
|---|----------|----------|---------|--------|---------------|
| UPD-BRC-1 | **Medium** | Missing Enum for `status` | Same as BRC-DTO-1 — `status` is typed as `?string` but should use `?BarcodeStatusEnum`. | Type safety. | Change type to `?BarcodeStatusEnum` and cast in `fromRequest()`. |
| UPD-BRC-2 | **Low** | `fromRequest` signature inconsistency | `fromRequest()` takes `(array $data, ?int $userId)` while `BarcodeDTO::fromRequest()` takes `(array $data, string $barcodeId, ?int $userId)`. | Different signatures for create vs update DTOs. This is acceptable since update doesn't need `barcodeId`, but worth noting. | No fix needed — the difference is justified. |
| UPD-BRC-3 | **Low** | `toArray()` omits nulls | `toArray()` only includes non-null properties (lines 30-51). This is correct for partial updates but differs from `BarcodeDTO::toArray()` which includes all fields. | Inconsistent serialization behavior between create and update DTOs. | Document that this is intentional for partial updates. |

**No issues found for:** Immutability, `readonly`, factory method, type declarations (except status), nullable handling, default values, value objects, mapping, missing fields, unused properties, duplicate, validation duplication, serialization, naming, constructor complexity, SOLID, Laravel best practices.

---

### 7. LoginRequestDTO

**File:** `backend/app/DTOs/LoginRequestDTO.php`  
**Lines:** 21  
**Used by:** `AuthController::login()`, `AuthenticationService::login()`

#### Issues Found

| # | Severity | Category | Problem | Impact | Suggested Fix |
|---|----------|----------|---------|--------|---------------|
| LOG-DTO-1 | **Low** | Missing `toArray()` | This DTO does not have a `toArray()` method, unlike all other DTOs. | Inconsistent API. If serialization is needed later, it must be added. | Add `toArray()` for consistency, even if not currently used. |
| LOG-DTO-2 | **Low** | Naming inconsistency | Property `$login` represents username/email. The name is ambiguous — it could mean "login action" or "login identifier". | Minor readability concern. | Consider renaming to `$username` or `$identifier`. |

**No issues found for:** Immutability, `readonly`, factory method, type declarations, nullable handling, default values, enum usage, value objects, mapping, missing fields, unused properties, duplicate, validation duplication, serialization, constructor complexity, SOLID, Laravel best practices.

---

### 8. ChangePasswordDTO

**File:** `backend/app/DTOs/ChangePasswordDTO.php`  
**Lines:** 21  
**Used by:** `AuthController::changePassword()`, `AuthenticationService::changePassword()`

#### Issues Found

| # | Severity | Category | Problem | Impact | Suggested Fix |
|---|----------|----------|---------|--------|---------------|
| CHG-DTO-1 | **Low** | Missing `toArray()` | This DTO does not have a `toArray()` method. | Inconsistent with other DTOs. | Add `toArray()` for consistency. |
| CHG-DTO-2 | **Low** | `fromRequest()` uses `auth()->id()` as fallback | `userId: (int) ($data['user_id'] ?? auth()->id())` — if `user_id` is not provided, it falls back to the authenticated user's ID. This mixes DTO construction with authentication context. | The DTO should be pure data. Authentication context should be resolved before DTO creation. | Resolve `userId` in the controller and pass it explicitly to `fromRequest()`. |

**No issues found for:** Immutability, `readonly`, factory method, type declarations, nullable handling, default values, enum usage, value objects, mapping, missing fields, unused properties, duplicate, validation duplication, serialization, naming, constructor complexity, SOLID, Laravel best practices.

---

### 9. RegisterUserDTO

**File:** `backend/app/DTOs/RegisterUserDTO.php`  
**Lines:** 25  
**Used by:** `UserController::store()`, `UserService::create()`

#### Issues Found

| # | Severity | Category | Problem | Impact | Suggested Fix |
|---|----------|----------|---------|--------|---------------|
| REG-DTO-1 | **Low** | Missing `toArray()` | This DTO does not have a `toArray()` method. | Inconsistent with other DTOs. | Add `toArray()` for consistency. |
| REG-DTO-2 | **Low** | `roleIds` type is `?array` | `$roleIds` is typed as `?array` with default `null`. The `UserService::create()` checks `if (!empty($dto->roleIds))`. | Works correctly, but `?array` with `null` default means the service must handle both `null` and `[]`. | Consider defaulting to `[]` instead of `null` to simplify the service check. |

**No issues found for:** Immutability, `readonly`, factory method, type declarations, nullable handling, default values, enum usage, value objects, mapping, missing fields, unused properties, duplicate, validation duplication, serialization, naming, constructor complexity, SOLID, Laravel best practices.

---

### 10. VerifyUserDTO

**File:** `backend/app/DTOs/VerifyUserDTO.php`  
**Lines:** 19  
**Used by:** `UserController::verify()`, `UserService::verify()`

#### Issues Found

| # | Severity | Category | Problem | Impact | Suggested Fix |
|---|----------|----------|---------|--------|---------------|
| VER-DTO-1 | **Medium** | Missing Enum for `status` | The `status` property is typed as `string` but should use `UserStatusEnum`. The `fromRequest()` passes `$data['status']` as a raw string. | Type safety — any string can be passed. If the enum values change, this DTO won't catch it. | Change type to `UserStatusEnum` and cast in `fromRequest()`: `status: UserStatusEnum::from($data['status'])`. |
| VER-DTO-2 | **Low** | Missing `toArray()` | This DTO does not have a `toArray()` method. | Inconsistent with other DTOs. | Add `toArray()` for consistency. |
| VER-DTO-3 | **Low** | `fromRequest()` called inconsistently | In `UserController::verify()`, the DTO is manually constructed: `new VerifyUserDTO(userId: $id, status: ...)` instead of using `VerifyUserDTO::fromRequest()`. | The `fromRequest()` factory method exists but is not used. | Use `VerifyUserDTO::fromRequest()` in the controller for consistency. |

**No issues found for:** Immutability, `readonly`, factory method, type declarations (except status), nullable handling, default values, value objects, mapping, missing fields, unused properties, duplicate, validation duplication, serialization, naming, constructor complexity, SOLID, Laravel best practices.

---

## Cross-Cutting Issues

### Critical Issues

**None found.**

### High Priority Issues

| # | Severity | Category | DTOs Affected | Problem | Suggested Fix |
|---|----------|----------|---------------|---------|---------------|
| H-1 | **High** | Missing Enum Types | `BarcodeDTO`, `UpdateBarcodeDTO`, `VerifyUserDTO` | Three DTOs use raw `string` types for fields that have corresponding Enums (`BarcodeStatusEnum`, `UserStatusEnum`). This bypasses type safety — invalid string values will only be caught at the database level (or not at all). | Replace `string` with the appropriate Enum type in constructor and cast in `fromRequest()`. |

### Medium Issues

| # | Severity | Category | DTOs Affected | Problem | Suggested Fix |
|---|----------|----------|---------------|---------|---------------|
| M-1 | **Medium** | Missing Enum Types | `BarcodeDTO::status`, `UpdateBarcodeDTO::status` | `status` should be `BarcodeStatusEnum` instead of `string`/`?string`. | Use `BarcodeStatusEnum`. |
| M-2 | **Medium** | Missing Enum Types | `VerifyUserDTO::status` | `status` should be `UserStatusEnum` instead of `string`. | Use `UserStatusEnum`. |

### Low Issues

| # | Severity | Category | DTOs Affected | Problem | Suggested Fix |
|---|----------|----------|---------------|---------|---------------|
| L-1 | **Low** | Missing `toArray()` | `LoginRequestDTO`, `ChangePasswordDTO`, `RegisterUserDTO`, `VerifyUserDTO` | 4 out of 10 DTOs are missing `toArray()` methods. While these DTOs are not currently serialized to arrays, the inconsistency is a maintenance concern. | Add `toArray()` to all DTOs for consistency. |
| L-2 | **Low** | Missing Update DTOs | `SiteDTO`, `MaterialTypeDTO`, `MaterialModelDTO`, `MaterialDTO` | 4 "create" DTOs exist but have no corresponding "update" DTO. Services accept raw arrays for updates instead. | Create `UpdateSiteDTO`, `UpdateMaterialTypeDTO`, `UpdateMaterialModelDTO`, `UpdateMaterialDTO`. |
| L-3 | **Low** | `fromRequest()` coupling | `ChangePasswordDTO` | `fromRequest()` calls `auth()->id()` internally, coupling DTO construction to the authentication context. | Pass `userId` from the controller instead. |
| L-4 | **Low** | Hardcoded value | `BarcodeDTO` | `toArray()` hardcodes `'is_active' => true`. | Make `isActive` a constructor parameter. |
| L-5 | **Low** | Naming | `LoginRequestDTO` | Property `$login` is ambiguous (login identifier vs login action). | Rename to `$identifier` or `$username`. |

---

## Summary

| Metric | Count |
|--------|-------|
| **Critical Issues** | 0 |
| **High Priority Issues** | 1 |
| **Medium Issues** | 2 |
| **Low Issues** | 5 |
| **Total Issues** | 8 (cross-cutting) + per-DTO issues |
| **DTOs with No Issues** | 0 (all have at least low-severity findings) |

### Key Takeaways

1. **Missing Enum Types (High)**: `BarcodeDTO`, `UpdateBarcodeDTO`, and `VerifyUserDTO` use raw `string` for fields that have dedicated Enums (`BarcodeStatusEnum`, `UserStatusEnum`). This is a type safety concern — invalid values won't be caught at the DTO level.
2. **Missing `toArray()` (Low)**: 4 out of 10 DTOs lack `toArray()` methods. While not currently needed for serialization, this inconsistency should be addressed.
3. **Missing Update DTOs (Low)**: 4 "create" DTOs exist without corresponding "update" DTOs. Services accept raw arrays for updates instead of typed DTOs.
4. **Overall Quality**: The DTO layer is well-structured with consistent patterns:
   - ✅ All DTOs use `readonly class` (immutability)
   - ✅ All DTOs have `fromRequest()` factory methods
   - ✅ All DTOs have proper type declarations (except enum fields)
   - ✅ All DTOs handle nullable fields correctly
   - ✅ No unused properties, duplicate DTOs, or constructor complexity issues
   - ✅ Clean separation of concerns — DTOs are pure data containers
