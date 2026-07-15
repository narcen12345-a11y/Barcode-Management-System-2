# Form Request Layer Audit Report

> **Generated:** 2026-07-15  
> **Scope:** All 19 Form Request Classes  
> **Methodology:** Manual code inspection against 20-point checklist  
> **Status:** ⚠️ Issues Found

---

## Audit Checklist Summary

| # | Check | Status |
|---|-------|--------|
| 1 | Validation rules completeness | ⚠️ 2 issues |
| 2 | `authorize()` implementation | ⚠️ 1 issue |
| 3 | Rule consistency | ⚠️ 1 issue |
| 4 | Enum validation | ✅ All correct |
| 5 | Exists/Unique rules | ✅ All correct |
| 6 | Custom validation | ✅ N/A |
| 7 | Sanitization | ⚠️ 1 issue |
| 8 | Conditional validation | ✅ All correct |
| 9 | Nullable handling | ✅ All correct |
| 10 | Array validation | ✅ All correct |
| 11 | File validation | ✅ N/A |
| 12 | Password validation | ⚠️ 1 issue |
| 13 | Security concerns | ⚠️ 1 issue |
| 14 | Rule duplication | ⚠️ 1 issue |
| 15 | Localization readiness | ⚠️ 1 issue |
| 16 | Custom messages | ⚠️ 1 issue |
| 17 | Performance | ✅ All correct |
| 18 | Laravel best practices | ⚠️ 2 issues |
| 19 | API consistency | ⚠️ 1 issue |
| 20 | Validation gaps | ⚠️ 2 issues |

---

## Request-by-Request Audit

---

### 1. LoginRequest

**File:** `backend/app/Http/Requests/LoginRequest.php`  
**Lines:** 31  
**Used by:** `AuthController::login()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| LOGIN-RQ-1 | **Low** | Missing `prepareForValidation()` | No sanitization. The `login` field accepts both username and email, but no trimming or lowercasing is applied. | User could accidentally include whitespace. | Add `prepareForValidation()` to trim and lowercase the `login` field. |
| LOGIN-RQ-2 | **Low** | Missing `max` for password | Password has `min:6` but no `max` length. | Very long passwords could cause DoS during hashing. | Add `max:100` to password rule. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Custom messages, Performance, Laravel best practices, API consistency, Validation gaps.

---

### 2. ChangePasswordRequest

**File:** `backend/app/Http/Requests/ChangePasswordRequest.php`  
**Lines:** 31  
**Used by:** `AuthController::changePassword()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| CHGPW-RQ-1 | **Low** | Missing `current_password` validation | `current_password` only has `required` and `string` rules. No minimum length or format validation. | Weak validation for current password field. | Add `min:6` to `current_password`. |
| CHGPW-RQ-2 | **Low** | Missing `confirmed` rule | `new_password` does not have a `confirmed` rule, so there's no `new_password_confirmation` field required. | Users might mistype their new password with no way to catch it. | Add `confirmed` to `new_password` and add `new_password_confirmation` field. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Custom messages, Performance, Laravel best practices, API consistency, Validation gaps.

---

### 3. CreateUserRequest

**File:** `backend/app/Http/Requests/CreateUserRequest.php`  
**Lines:** 41  
**Used by:** `UserController::store()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| CRUSR-RQ-1 | **Low** | Missing `confirmed` rule for password | Password has `min:6` and `max:100` but no `confirmed` rule. | Users might mistype their password with no way to catch it. | Add `confirmed` to `password` and add `password_confirmation` field. |
| CRUSR-RQ-2 | **Low** | Missing `regex` for username | Username only has `min:3`, `max:50`, and `unique`. No character restrictions. | Usernames could contain spaces or special characters that cause issues. | Add `regex:/^[a-zA-Z0-9_]+$/` to restrict username characters. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Custom messages, Performance, Laravel best practices, API consistency, Validation gaps.

---

### 4. UpdateUserRequest

**File:** `backend/app/Http/Requests/UpdateUserRequest.php`  
**Lines:** 39  
**Used by:** `UserController::update()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| UPDUSR-RQ-1 | **Low** | Missing `confirmed` rule for password | Same as CRUSR-RQ-1 — password has no `confirmed` rule. | Users might mistype their password. | Add `confirmed` to `password`. |
| UPDUSR-RQ-2 | **Low** | Missing `regex` for username | Same as CRUSR-RQ-2 — no character restrictions on username. | Usernames could contain spaces or special characters. | Add `regex:/^[a-zA-Z0-9_]+$/`. |
| UPDUSR-RQ-3 | **Low** | Missing custom messages | Missing custom messages for `password.required`, `full_name.required`, `username.required`, `email.required` (present in CreateUserRequest but not here). | Inconsistent user experience — some validation errors show default Laravel messages. | Add consistent custom messages matching CreateUserRequest. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Performance, Laravel best practices, API consistency, Validation gaps.

---

### 5. VerifyUserRequest

**File:** `backend/app/Http/Requests/VerifyUserRequest.php`  
**Lines:** 29  
**Used by:** `UserController::verify()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| VERUSR-RQ-1 | **Low** | Missing `user_id` validation | The request only validates `status`. The `user_id` is taken from the route parameter, but there's no validation that the user exists. | If the route parameter is invalid, the error will surface at the service layer instead of the validation layer. | Add `exists:users,id` validation for the route parameter or handle in the controller. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Custom messages, Performance, Laravel best practices, API consistency, Validation gaps.

---

### 6. StoreSiteRequest

**File:** `backend/app/Http/Requests/StoreSiteRequest.php`  
**Lines:** 26  
**Used by:** `SiteController::store()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| STOSITE-RQ-1 | **Low** | Missing custom messages | No `messages()` method defined. | Default Laravel validation messages will be shown (in English). | Add Indonesian custom messages for consistency with other requests. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Performance, Laravel best practices, API consistency, Validation gaps.

---

### 7. UpdateSiteRequest

**File:** `backend/app/Http/Requests/UpdateSiteRequest.php`  
**Lines:** 28  
**Used by:** `SiteController::update()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| UPDSITE-RQ-1 | **Low** | Missing custom messages | No `messages()` method defined. | Same as STOSITE-RQ-1. | Add Indonesian custom messages. |
| UPDSITE-RQ-2 | **Low** | Route parameter mismatch | `$siteId = $this->route('site')` — the route parameter is `{site}` but the controller uses `int $id`. | If route-model binding is enabled, `$this->route('site')` returns a Model instance, not an ID. | Ensure route parameter name matches what's expected. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Performance, Laravel best practices, API consistency, Validation gaps.

---

### 8. StoreMaterialTypeRequest

**File:** `backend/app/Http/Requests/StoreMaterialTypeRequest.php`  
**Lines:** 22  
**Used by:** `MaterialTypeController::store()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| STOMT-RQ-1 | **Low** | Missing custom messages | No `messages()` method defined. | Default Laravel validation messages. | Add Indonesian custom messages. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Performance, Laravel best practices, API consistency, Validation gaps.

---

### 9. UpdateMaterialTypeRequest

**File:** `backend/app/Http/Requests/UpdateMaterialTypeRequest.php`  
**Lines:** 24  
**Used by:** `MaterialTypeController::update()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| UPDMT-RQ-1 | **Low** | Missing custom messages | No `messages()` method defined. | Default Laravel validation messages. | Add Indonesian custom messages. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Performance, Laravel best practices, API consistency, Validation gaps.

---

### 10. StoreMaterialModelRequest

**File:** `backend/app/Http/Requests/StoreMaterialModelRequest.php`  
**Lines:** 23  
**Used by:** `MaterialModelController::store()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| STOMM-RQ-1 | **Low** | Missing custom messages | No `messages()` method defined. | Default Laravel validation messages. | Add Indonesian custom messages. |
| STOMM-RQ-2 | **Low** | Missing `unique` for name | `name` has no `unique` rule. The same model name could be created under different material types. | If business rules require unique names per material type, this is a gap. | Add `unique:material_models,name` or a composite unique rule. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Performance, Laravel best practices, API consistency.

---

### 11. UpdateMaterialModelRequest

**File:** `backend/app/Http/Requests/UpdateMaterialModelRequest.php`  
**Lines:** 23  
**Used by:** `MaterialModelController::update()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| UPDMM-RQ-1 | **Low** | Missing custom messages | No `messages()` method defined. | Default Laravel validation messages. | Add Indonesian custom messages. |
| UPDMM-RQ-2 | **Low** | Missing `unique` for name | Same as STOMM-RQ-2 — no unique check on name. | Duplicate model names possible. | Add `unique:material_models,name,{id}`. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Performance, Laravel best practices, API consistency.

---

### 12. StoreMaterialRequest

**File:** `backend/app/Http/Requests/StoreMaterialRequest.php`  
**Lines:** 25  
**Used by:** `MaterialController::store()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| STOMAT-RQ-1 | **Low** | Missing custom messages | No `messages()` method defined. | Default Laravel validation messages. | Add Indonesian custom messages. |
| STOMAT-RQ-2 | **Low** | Missing composite unique | `material_code` is unique globally, but there's no check that `(material_type_id, material_model_id, name)` is unique. | Duplicate materials possible with same name under same type/model. | Add custom validation or composite unique index. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Performance, Laravel best practices, API consistency.

---

### 13. UpdateMaterialRequest

**File:** `backend/app/Http/Requests/UpdateMaterialRequest.php`  
**Lines:** 27  
**Used by:** `MaterialController::update()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| UPDMAT-RQ-1 | **Low** | Missing custom messages | No `messages()` method defined. | Default Laravel validation messages. | Add Indonesian custom messages. |
| UPDMAT-RQ-2 | **Low** | Missing composite unique | Same as STOMAT-RQ-2. | Duplicate materials possible. | Add custom validation. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Performance, Laravel best practices, API consistency.

---

### 14. StoreBarcodeRequest

**File:** `backend/app/Http/Requests/StoreBarcodeRequest.php`  
**Lines:** 40  
**Used by:** `BarcodeController::store()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| STOBRC-RQ-1 | **Low** | Enum message key fragility | Custom message key `status.Illuminate\Validation\Rules\Enum` is fragile — it depends on the fully qualified class name of the Enum rule. | If Laravel changes the Enum rule class name, this message will stop working. | Use a more stable key or use `Rule::enum()->message(...)`. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Performance, Laravel best practices, API consistency, Validation gaps.

---

### 15. UpdateBarcodeRequest

**File:** `backend/app/Http/Requests/UpdateBarcodeRequest.php`  
**Lines:** 42  
**Used by:** `BarcodeController::update()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| UPDBRC-RQ-1 | **Low** | Enum message key fragility | Same as STOBRC-RQ-1 — fragile Enum message key. | Message may stop working. | Use a more stable key. |
| UPDBRC-RQ-2 | **Low** | Route parameter mismatch | `$barcodeId = $this->route('barcode')` — the route parameter is `{barcode}` but the controller uses `int $id`. | If route-model binding is enabled, `$this->route('barcode')` returns a Model instance. | Ensure consistency. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Performance, Laravel best practices, API consistency, Validation gaps.

---

### 16. StoreRoleRequest

**File:** `backend/app/Http/Requests/StoreRoleRequest.php`  
**Lines:** 25  
**Used by:** `RoleController::store()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| STOROL-RQ-1 | **Low** | Missing custom messages | No `messages()` method defined. | Default Laravel validation messages. | Add Indonesian custom messages. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Performance, Laravel best practices, API consistency, Validation gaps.

---

### 17. UpdateRoleRequest

**File:** `backend/app/Http/Requests/UpdateRoleRequest.php`  
**Lines:** 27  
**Used by:** `RoleController::update()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| UPDROL-RQ-1 | **Low** | Missing custom messages | No `messages()` method defined. | Default Laravel validation messages. | Add Indonesian custom messages. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Performance, Laravel best practices, API consistency, Validation gaps.

---

### 18. StorePermissionRequest

**File:** `backend/app/Http/Requests/StorePermissionRequest.php`  
**Lines:** 24  
**Used by:** `PermissionController::store()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| STOPERM-RQ-1 | **Low** | Missing custom messages | No `messages()` method defined. | Default Laravel validation messages. | Add Indonesian custom messages. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Performance, Laravel best practices, API consistency, Validation gaps.

---

### 19. UpdatePermissionRequest

**File:** `backend/app/Http/Requests/UpdatePermissionRequest.php`  
**Lines:** 26  
**Used by:** `PermissionController::update()`

#### Issues Found

| # | Severity | Category | Problem | Risk | Suggested Fix |
|---|----------|----------|---------|------|---------------|
| UPDOPERM-RQ-1 | **Low** | Missing custom messages | No `messages()` method defined. | Default Laravel validation messages. | Add Indonesian custom messages. |

**No issues found for:** `authorize()`, Enum validation, Exists/Unique rules, Custom validation, Conditional validation, Nullable handling, Array validation, File validation, Security, Rule duplication, Localization, Performance, Laravel best practices, API consistency, Validation gaps.

---

## Cross-Cutting Issues

### Critical Issues

**None found.**

### High Priority Issues

**None found.**

### Medium Issues

| # | Severity | Category | Requests Affected | Problem | Suggested Fix |
|---|----------|----------|-------------------|---------|---------------|
| M-1 | **Medium** | Missing `confirmed` rule | `ChangePasswordRequest`, `CreateUserRequest`, `UpdateUserRequest` | Password fields lack `confirmed` rule, meaning no confirmation field is required. Users can mistype their password with no way to catch the error. | Add `confirmed` to all password rules and require `password_confirmation` field. |
| M-2 | **Medium** | Missing `authorize()` checks | **All 19 requests** | Every request returns `return true` in `authorize()`. Authorization is handled entirely at the route level via `permission:*` middleware. | This is a design choice — authorization at route level vs request level. Both are valid Laravel patterns. However, if authorization logic needs to be request-specific (e.g., user can only update their own profile), the request-level `authorize()` method should be used. |

### Low Issues

| # | Severity | Category | Requests Affected | Problem | Suggested Fix |
|---|----------|----------|-------------------|---------|---------------|
| L-1 | **Low** | Missing custom messages | 12 out of 19 requests | Only 7 requests have custom `messages()` methods (LoginRequest, ChangePasswordRequest, CreateUserRequest, UpdateUserRequest, VerifyUserRequest, StoreBarcodeRequest, UpdateBarcodeRequest). The remaining 12 use default Laravel English messages. | Add Indonesian custom messages to all requests for consistent user experience. |
| L-2 | **Low** | Missing `regex` for username | `CreateUserRequest`, `UpdateUserRequest` | Username fields have no character restrictions. | Add `regex:/^[a-zA-Z0-9_]+$/`. |
| L-3 | **Low** | Missing `max` for password | `LoginRequest` | Password has `min:6` but no `max`. | Add `max:100`. |
| L-4 | **Low** | Missing `min` for current_password | `ChangePasswordRequest` | `current_password` has no minimum length validation. | Add `min:6`. |
| L-5 | **Low** | Missing `unique` for name | `StoreMaterialModelRequest`, `UpdateMaterialModelRequest` | Material model name has no unique validation. | Add `unique:material_models,name`. |
| L-6 | **Low** | Missing composite unique | `StoreMaterialRequest`, `UpdateMaterialRequest` | No composite unique check for `(material_type_id, material_model_id, name)`. | Add custom validation. |
| L-7 | **Low** | Fragile Enum message key | `StoreBarcodeRequest`, `UpdateBarcodeRequest` | Custom message key uses FQCN of Enum rule class. | Use a more stable key. |
| L-8 | **Low** | Route parameter mismatch | `UpdateSiteRequest`, `UpdateBarcodeRequest` | Route parameter name may not match what the request expects. | Ensure consistency between route params and request methods. |
| L-9 | **Low** | Missing `prepareForValidation()` | `LoginRequest` | No input sanitization (trim, lowercase). | Add `prepareForValidation()`. |

---

## Summary

| Metric | Count |
|--------|-------|
| **Critical Issues** | 0 |
| **High Priority Issues** | 0 |
| **Medium Issues** | 2 |
| **Low Issues** | 9 |
| **Total Issues** | 11 |
| **Requests with No Issues** | 0 (all have at least low-severity findings) |

### Key Takeaways

1. **Authorization Design Choice (Medium)**: All 19 requests return `return true` in `authorize()`. Authorization is delegated entirely to route-level `permission:*` middleware. This is a valid Laravel pattern, but it means request-level authorization (e.g., "user can only edit their own profile") would require changes.

2. **Missing `confirmed` Rule (Medium)**: `ChangePasswordRequest`, `CreateUserRequest`, and `UpdateUserRequest` lack `confirmed` on password fields. Users can mistype their password with no way to catch the error.

3. **Missing Custom Messages (Low)**: 12 out of 19 requests lack custom `messages()` methods, falling back to default Laravel English messages. Only 7 requests have Indonesian custom messages.

4. **Overall Quality**: The Form Request layer is well-structured:
   - ✅ **Enum validation** is correctly implemented in `StoreBarcodeRequest`, `UpdateBarcodeRequest`, and `VerifyUserRequest` using `new Enum(...)`.
   - ✅ **Exists/Unique rules** are correctly implemented with proper ID exclusion on update requests.
   - ✅ **Nullable handling** is consistent — all nullable fields use `nullable` rule.
   - ✅ **Array validation** is correctly implemented for `role_ids` and `permission_ids`.
   - ✅ **No custom validation closures** — all rules are declarative and testable.
   - ✅ **No file validation needed** — no file uploads in the system.
   - ✅ **Performance** — no expensive validation rules (no unique checks on large datasets without indexes).
   - ✅ **Rule duplication** is minimal — Store/Update pairs share the same rules with appropriate `sometimes` vs `required` differences.

### Requests with Custom Messages (Good)

| Request | Has `messages()` |
|---------|-----------------|
| LoginRequest | ✅ |
| ChangePasswordRequest | ✅ |
| CreateUserRequest | ✅ |
| UpdateUserRequest | ✅ |
| VerifyUserRequest | ✅ |
| StoreBarcodeRequest | ✅ |
| UpdateBarcodeRequest | ✅ |
| StoreSiteRequest | ❌ |
| UpdateSiteRequest | ❌ |
| StoreMaterialTypeRequest | ❌ |
| UpdateMaterialTypeRequest | ❌ |
| StoreMaterialModelRequest | ❌ |
| UpdateMaterialModelRequest | ❌ |
| StoreMaterialRequest | ❌ |
| UpdateMaterialRequest | ❌ |
| StoreRoleRequest | ❌ |
| UpdateRoleRequest | ❌ |
| StorePermissionRequest | ❌ |
| UpdatePermissionRequest | ❌ |
