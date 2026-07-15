# Frontend ↔ Backend Alignment Audit

**Date:** 2026-07-15  
**Scope:** Full audit of all 7 modules (Sites, Material Types, Material Models, Materials, Permissions, Roles, Users, Barcodes)  
**Coverage:** Controllers, FormRequests, Resources, Repositories, Services, Routes (backend) ↔ Services, Pages, Routes (frontend)

---

## 1. Sites Module

### Backend API Contract
| Method | Endpoint | Controller Method | FormRequest | Resource |
|--------|----------|-------------------|-------------|----------|
| GET | `/api/sites` | `SiteController@index` | — | `SiteResource` collection |
| GET | `/api/sites/all` | `SiteController@all` | — | `SiteResource` collection |
| GET | `/api/sites/{id}` | `SiteController@show` | — | `SiteResource` |
| POST | `/api/sites` | `SiteController@store` | `StoreSiteRequest` | `SiteResource` |
| PUT | `/api/sites/{id}` | `SiteController@update` | `UpdateSiteRequest` | `SiteResource` |
| DELETE | `/api/sites/{id}` | `SiteController@destroy` | — | — |
| POST | `/api/sites/{id}/restore` | `SiteController@restore` | — | — |

### StoreSiteRequest Fields
| Field | Rules |
|-------|-------|
| `site_id` | required, string, max:50, unique:sites |
| `site_name` | required, string, max:255 |
| `region` | nullable, string, max:100 |
| `address` | nullable, string |
| `latitude` | nullable, string, max:50 |
| `longitude` | nullable, string, max:50 |
| `is_active` | sometimes, boolean |

### UpdateSiteRequest Fields
Same as Store, but all fields are `sometimes` and `site_id` unique ignores own ID via route param.

### SiteResource Fields
`id`, `site_id`, `site_name`, `region`, `address`, `latitude`, `longitude`, `is_active`, `created_at`, `updated_at`, `deleted_at`

### Frontend Service
`siteService = new BaseCrudService('/sites')` ✅

### Frontend List Page (`SiteListPage.jsx`)
- Columns: `site_id`, `site_name`, `region`, `address`, `is_active` (status), `created_at` ✅
- Filters: `is_active` (select) ✅
- Actions: Edit (`update-site`), Delete (`delete-site`), Restore (`delete-site`) ✅
- Search: ✅ (via `useCrud` → `search` param → backend searches `site_id`, `site_name`)
- Pagination: ✅

### Frontend Form Page (`SiteFormPage.jsx`)
- Zod schema matches `StoreSiteRequest` exactly ✅
  - `site_id`: required, max:50 ✅
  - `site_name`: required, max:255 ✅
  - `region`: optional, max:100 ✅
  - `address`: optional ✅
  - `latitude`: optional, max:50 ✅
  - `longitude`: optional, max:50 ✅
  - `is_active`: optional boolean, default true ✅
- Edit mode loads data via `getById(id)` ✅
- Submit uses `useFormSubmit` with `create`/`update` method ✅

### Alignment: ✅ **FULLY ALIGNED**

---

## 2. Material Types Module

### Backend API Contract
| Method | Endpoint | Controller Method | FormRequest | Resource |
|--------|----------|-------------------|-------------|----------|
| GET | `/api/material-types` | `MaterialTypeController@index` | — | `MaterialTypeResource` collection |
| GET | `/api/material-types/all` | `MaterialTypeController@all` | — | `MaterialTypeResource` collection |
| GET | `/api/material-types/{id}` | `MaterialTypeController@show` | — | `MaterialTypeResource` |
| POST | `/api/material-types` | `MaterialTypeController@store` | `StoreMaterialTypeRequest` | `MaterialTypeResource` |
| PUT | `/api/material-types/{id}` | `MaterialTypeController@update` | `UpdateMaterialTypeRequest` | `MaterialTypeResource` |
| DELETE | `/api/material-types/{id}` | `MaterialTypeController@destroy` | — | — |
| POST | `/api/material-types/{id}/restore` | `MaterialTypeController@restore` | — | — |

### StoreMaterialTypeRequest Fields
| Field | Rules |
|-------|-------|
| `name` | required, string, max:100, unique:material_types |
| `description` | nullable, string |
| `is_active` | sometimes, boolean |

### MaterialTypeResource Fields
`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`

### Frontend Service
`materialTypeService = new BaseCrudService('/material-types')` ✅

### Frontend List Page (`MaterialTypeListPage.jsx`)
- Columns: `name`, `description`, `is_active` (status), `created_at` ✅
- Filters: `is_active` (select) ✅
- Actions: Edit (`update-material-type`), Delete (`delete-material-type`), Restore (`delete-material-type`) ✅
- Search: ✅

### Frontend Form Page (`MaterialTypeFormPage.jsx`)
- Zod schema matches `StoreMaterialTypeRequest` exactly ✅
  - `name`: required, max:100 ✅
  - `description`: optional ✅
  - `is_active`: optional boolean, default true ✅

### Alignment: ✅ **FULLY ALIGNED**

---

## 3. Material Models Module

### Backend API Contract
| Method | Endpoint | Controller Method | FormRequest | Resource |
|--------|----------|-------------------|-------------|----------|
| GET | `/api/material-models` | `MaterialModelController@index` | — | `MaterialModelResource` collection |
| GET | `/api/material-models/all` | `MaterialModelController@all` | — | `MaterialModelResource` collection |
| GET | `/api/material-models/by-material-type/{materialTypeId}` | `MaterialModelController@byMaterialType` | — | `MaterialModelResource` collection |
| GET | `/api/material-models/{id}` | `MaterialModelController@show` | — | `MaterialModelResource` |
| POST | `/api/material-models` | `MaterialModelController@store` | `StoreMaterialModelRequest` | `MaterialModelResource` |
| PUT | `/api/material-models/{id}` | `MaterialModelController@update` | `UpdateMaterialModelRequest` | `MaterialModelResource` |
| DELETE | `/api/material-models/{id}` | `MaterialModelController@destroy` | — | — |
| POST | `/api/material-models/{id}/restore` | `MaterialModelController@restore` | — | — |

### StoreMaterialModelRequest Fields
| Field | Rules |
|-------|-------|
| `material_type_id` | required, integer, exists:material_types,id |
| `name` | required, string, max:100 |
| `description` | nullable, string |
| `is_active` | sometimes, boolean |

### MaterialModelResource Fields
`id`, `material_type_id`, `material_type` (whenLoaded), `name`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`

### Frontend Service
`materialModelService = new MaterialModelService()` extends `BaseCrudService('/material-models')` ✅
- Extra method: `getByMaterialType(materialTypeId)` → `GET /material-models/by-material-type/{id}` ✅

### Frontend List Page (`MaterialModelListPage.jsx`)
- Columns: `name`, `material_type` (custom render), `description`, `is_active` (status), `created_at` ✅
- Filters: `is_active` (select) ✅
- Actions: Edit (`update-material-model`), Delete (`delete-material-model`), Restore (`delete-material-model`) ✅
- Search: ✅

### Frontend Form Page (`MaterialModelFormPage.jsx`)
- Zod schema matches `StoreMaterialModelRequest` exactly ✅
  - `material_type_id`: required ✅ (dropdown loaded from `materialTypeService.getAllUnpaginated()`)
  - `name`: required, max:100 ✅
  - `description`: optional ✅
  - `is_active`: optional boolean, default true ✅
- On submit, converts `material_type_id` to Number ✅

### Alignment: ✅ **FULLY ALIGNED**

---

## 4. Materials Module

### Backend API Contract
| Method | Endpoint | Controller Method | FormRequest | Resource |
|--------|----------|-------------------|-------------|----------|
| GET | `/api/materials` | `MaterialController@index` | — | `MaterialResource` collection |
| GET | `/api/materials/all` | `MaterialController@all` | — | `MaterialResource` collection |
| GET | `/api/materials/{id}` | `MaterialController@show` | — | `MaterialResource` |
| POST | `/api/materials` | `MaterialController@store` | `StoreMaterialRequest` | `MaterialResource` |
| PUT | `/api/materials/{id}` | `MaterialController@update` | `UpdateMaterialRequest` | `MaterialResource` |
| DELETE | `/api/materials/{id}` | `MaterialController@destroy` | — | — |
| POST | `/api/materials/{id}/restore` | `MaterialController@restore` | — | — |

### StoreMaterialRequest Fields
| Field | Rules |
|-------|-------|
| `material_type_id` | required, integer, exists:material_types,id |
| `material_model_id` | required, integer, exists:material_models,id |
| `material_code` | required, string, max:50, unique:materials |
| `name` | required, string, max:255 |
| `description` | nullable, string |
| `is_active` | sometimes, boolean |

### MaterialResource Fields
`id`, `material_type_id`, `material_type` (whenLoaded), `material_model_id`, `material_model` (whenLoaded), `material_code`, `name`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`

### Frontend Service
`materialService = new BaseCrudService('/materials')` ✅

### Frontend List Page (`MaterialListPage.jsx`)
- Columns: `material_code`, `name`, `material_type` (custom), `material_model` (custom), `description`, `is_active` (status), `created_at` ✅
- Filters: `is_active` (select) ✅
- Actions: Edit (`update-material`), Delete (`delete-material`), Restore (`delete-material`) ✅
- Search: ✅

### Frontend Form Page (`MaterialFormPage.jsx`)
- Zod schema matches `StoreMaterialRequest` exactly ✅
  - `material_type_id`: required ✅ (dropdown from `materialTypeService.getAllUnpaginated()`)
  - `material_model_id`: required ✅ (dependent dropdown via `materialModelService.getByMaterialType()`)
  - `material_code`: required, max:50 ✅
  - `name`: required, max:255 ✅
  - `description`: optional ✅
  - `is_active`: optional boolean, default true ✅
- On submit, converts IDs to Number ✅

### Alignment: ✅ **FULLY ALIGNED**

---

## 5. Permissions Module

### Backend API Contract
| Method | Endpoint | Controller Method | FormRequest | Resource |
|--------|----------|-------------------|-------------|----------|
| GET | `/api/permissions` | `PermissionController@index` | — | `PermissionResource` collection |
| GET | `/api/permissions/all` | `PermissionController@all` | — | `PermissionResource` collection |
| GET | `/api/permissions/{id}` | `PermissionController@show` | — | `PermissionResource` |
| POST | `/api/permissions` | `PermissionController@store` | `StorePermissionRequest` | `PermissionResource` |
| PUT | `/api/permissions/{id}` | `PermissionController@update` | `UpdatePermissionRequest` | `PermissionResource` |
| DELETE | `/api/permissions/{id}` | `PermissionController@destroy` | — | — |
| POST | `/api/permissions/{id}/restore` | `PermissionController@restore` | — | — |

### StorePermissionRequest Fields
| Field | Rules |
|-------|-------|
| `name` | required, string, max:100, unique:permissions |
| `display_name` | required, string, max:100 |
| `module` | required, string, max:50 |
| `description` | nullable, string |
| `is_active` | sometimes, boolean |

### PermissionResource Fields
`id`, `name`, `display_name`, `module`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`

### Frontend Service
`permissionService = new BaseCrudService('/permissions')` ✅

### Frontend List Page (`PermissionListPage.jsx`)
- Columns: `name`, `display_name`, `module`, `description`, `is_active` (status), `created_at` ✅
- Filters: `module` (text), `is_active` (select) ✅
- Actions: Edit (`update-permission`), Delete (`delete-permission`), Restore (`delete-permission`) ✅
- Search: ✅

### Frontend Form Page (`PermissionFormPage.jsx`)
- Zod schema matches `StorePermissionRequest` exactly ✅
  - `name`: required, max:100 ✅
  - `display_name`: required, max:100 ✅
  - `module`: required, max:50 ✅
  - `description`: optional ✅
  - `is_active`: optional boolean, default true ✅

### Alignment: ✅ **FULLY ALIGNED**

---

## 6. Roles Module

### Backend API Contract
| Method | Endpoint | Controller Method | FormRequest | Resource |
|--------|----------|-------------------|-------------|----------|
| GET | `/api/roles` | `RoleController@index` | — | `RoleResource` collection |
| GET | `/api/roles/all` | `RoleController@all` | — | `RoleResource` collection |
| GET | `/api/roles/{id}` | `RoleController@show` | — | `RoleResource` (with `permissions` loaded) |
| POST | `/api/roles` | `RoleController@store` | `StoreRoleRequest` | `RoleResource` |
| PUT | `/api/roles/{id}` | `RoleController@update` | `UpdateRoleRequest` | `RoleResource` |
| DELETE | `/api/roles/{id}` | `RoleController@destroy` | — | — |
| POST | `/api/roles/{id}/restore` | `RoleController@restore` | — | — |

### StoreRoleRequest Fields
| Field | Rules |
|-------|-------|
| `name` | required, string, max:50, unique:roles |
| `display_name` | required, string, max:100 |
| `description` | nullable, string |
| `is_active` | sometimes, boolean |
| `permission_ids` | sometimes, array |
| `permission_ids.*` | exists:permissions,id |

### RoleResource Fields
`id`, `name`, `display_name`, `description`, `is_active`, `permissions` (whenLoaded), `created_at`, `updated_at`, `deleted_at`

### Frontend Service
`roleService = new BaseCrudService('/roles')` ✅

### Frontend List Page (`RoleListPage.jsx`)
- Columns: `name`, `display_name`, `description`, `permissions` (custom: shows up to 3 badges), `is_active` (status), `created_at` ✅
- Filters: `is_active` (select) ✅
- Actions: Edit (`update-role`), Delete (`delete-role`), Restore (`delete-role`) ✅
- Search: ✅

### Frontend Form Page (`RoleFormPage.jsx`)
- Zod schema matches `StoreRoleRequest` exactly ✅
  - `name`: required, max:50 ✅
  - `display_name`: required, max:100 ✅
  - `description`: optional ✅
  - `is_active`: optional boolean, default true ✅
  - `permission_ids`: optional array of numbers, default [] ✅
- Permission checklist grouped by module ✅
- Loads permissions from `permissionService.getAllUnpaginated()` ✅
- On submit, sends `permission_ids` array ✅

### Alignment: ✅ **FULLY ALIGNED**

---

## 7. Users Module

### Backend API Contract
| Method | Endpoint | Controller Method | FormRequest | Resource |
|--------|----------|-------------------|-------------|----------|
| GET | `/api/users` | `UserController@index` | — | `UserResource` collection |
| GET | `/api/users/{id}` | `UserController@show` | — | `UserResource` (with `roles` loaded) |
| POST | `/api/users` | `UserController@store` | `CreateUserRequest` | `UserResource` |
| PUT | `/api/users/{id}` | `UserController@update` | `UpdateUserRequest` | `UserResource` |
| DELETE | `/api/users/{id}` | `UserController@destroy` | — | — |
| POST | `/api/users/{id}/restore` | `UserController@restore` | — | — |
| POST | `/api/users/{id}/verify` | `UserController@verify` | `VerifyUserRequest` | `UserResource` |
| POST | `/api/users/{id}/activate` | `UserController@activate` | — | `UserResource` |
| POST | `/api/users/{id}/deactivate` | `UserController@deactivate` | — | `UserResource` |
| POST | `/api/users/{id}/reset-password` | `UserController@resetPassword` | — | `UserResource` |

### CreateUserRequest Fields
| Field | Rules |
|-------|-------|
| `username` | required, string, min:3, max:50, unique:users |
| `email` | required, email, max:255, unique:users |
| `password` | required, string, min:6, max:100, confirmed |
| `full_name` | required, string, max:255 |
| `role_ids` | sometimes, array |
| `role_ids.*` | exists:roles,id |

### UpdateUserRequest Fields
Same as Create, but all fields are `sometimes` and unique ignores own ID.

### UserResource Fields
`id`, `username`, `email`, `full_name`, `status`, `is_active`, `email_verified_at`, `last_login_at`, `password_changed_at`, `roles` (whenLoaded), `created_at`, `updated_at`, `deleted_at`

### Frontend Service
`userService = new UserService()` extends `BaseCrudService('/users')` ✅
- Extra methods: `verify()`, `activate()`, `deactivate()`, `resetPassword()` ✅

### Frontend List Page (`UserListPage.jsx`)
- Columns: `username`, `full_name`, `email`, `roles` (custom badges), `status` (colored), `created_at` ✅
- Filters: `status` (select), `is_active` (select) ✅
- Actions: Edit (`update-user`), Delete (`delete-user`), Restore (`delete-user`) ✅
- Extra actions: Activate (`activate-user`), Deactivate (`deactivate-user`), Reset Password (`reset-password`) ✅
- Search: ✅

### Frontend Form Page (`UserFormPage.jsx`)
- Zod schema matches `CreateUserRequest` exactly ✅
  - `username`: required, min:3, max:50 ✅
  - `email`: required, valid email, max:255 ✅
  - `password`: required (create) / optional (edit), min:6, max:100 ✅
  - `password_confirmation`: must match password (refine) ✅
  - `full_name`: required, max:255 ✅
  - `role_ids`: optional array of numbers, default [] ✅
- Role checklist loaded from `roleService.getAllUnpaginated()` ✅
- On submit, only sends password if provided (for edit) ✅

### Alignment: ✅ **FULLY ALIGNED**

---

## 8. Barcodes Module

### Backend API Contract
| Method | Endpoint | Controller Method | FormRequest | Resource |
|--------|----------|-------------------|-------------|----------|
| GET | `/api/barcodes` | `BarcodeController@index` | — | `BarcodeResource` collection |
| GET | `/api/barcodes/all` | `BarcodeController@all` | — | `BarcodeResource` collection |
| GET | `/api/barcodes/{id}` | `BarcodeController@show` | — | `BarcodeDetailResource` (with `histories.changedBy`) |
| GET | `/api/barcodes/by-barcode-id/{barcodeId}` | `BarcodeController@showByBarcodeId` | — | `BarcodeDetailResource` |
| POST | `/api/barcodes` | `BarcodeController@store` | `StoreBarcodeRequest` | `BarcodeResource` |
| PUT | `/api/barcodes/{id}` | `BarcodeController@update` | `UpdateBarcodeRequest` | `BarcodeResource` |
| DELETE | `/api/barcodes/{id}` | `BarcodeController@destroy` | — | — |
| POST | `/api/barcodes/{id}/restore` | `BarcodeController@restore` | — | — |
| GET | `/api/barcodes/{id}/history` | `BarcodeController@history` | — | `BarcodeHistoryResource` collection |

### StoreBarcodeRequest Fields
| Field | Rules |
|-------|-------|
| `material_id` | required, integer, exists:materials,id |
| `site_id` | required, integer, exists:sites,id |
| `serial_number` | required, string, max:255, unique:barcodes |
| `status` | required, string, Enum:BarcodeStatusEnum (NEW, OLD) |
| `description` | nullable, string |

### BarcodeResource Fields
`id`, `barcode_id`, `material` (whenLoaded), `site` (whenLoaded), `serial_number`, `status`, `description`, `is_active`, `created_by` (whenLoaded), `updated_by` (whenLoaded), `created_at`, `updated_at`, `deleted_at`

### BarcodeDetailResource Fields
Same as BarcodeResource + `histories` (whenLoaded)

### BarcodeHistoryResource Fields
`id`, `barcode_id`, `field_name`, `old_value`, `new_value`, `changed_by` (whenLoaded), `change_reason`, `created_at`

### Frontend Service
`barcodeService = new BarcodeService()` extends `BaseCrudService('/barcodes')` ✅
- Extra method: `getHistory(id, params)` → `GET /barcodes/{id}/history` ✅

### Frontend List Page (`BarcodeListPage.jsx`)
- Columns: `barcode_id`, `serial_number`, `material` (custom), `site` (custom), `status` (colored), `description`, `created_at` ✅
- Filters: `status` (select: NEW/OLD), `site_id` (select, loaded from API), `material_id` (select, loaded from API), `is_active` (select) ✅
- Actions: View (`read-barcode`), Edit (`update-barcode`), Delete (`delete-barcode`), Restore (`delete-barcode`) ✅
- Search: ✅ (searches `barcode_id`, `serial_number` on backend)

### Frontend Form Page (`BarcodeFormPage.jsx`)
- Zod schema matches `StoreBarcodeRequest` exactly ✅
  - `material_id`: required ✅ (dropdown from `materialService.getAllUnpaginated()`)
  - `site_id`: required ✅ (dropdown from `siteService.getAllUnpaginated()`)
  - `serial_number`: required, max:255 ✅
  - `status`: required ✅ (select with NEW/OLD options matching BarcodeStatusEnum)
  - `description`: optional ✅
- Status options match `BarcodeStatusEnum` labels ✅
- On submit, converts IDs to Number, sends `description` as null if empty ✅

### Frontend Detail Page (`BarcodeDetailPage.jsx`)
- 4 sections: Barcode Info, Material & Site, Audit Trail, History Timeline ✅
- History loads from dedicated paginated endpoint (`getHistory`) with fallback to detail resource ✅
- Timeline shows field changes with old/new values, user, timestamp ✅
- Status colors match enum (NEW=cyan, OLD=amber) ✅

### Alignment: ✅ **FULLY ALIGNED**

---

## 9. Cross-Cutting Concerns

### 9.1 API Response Shape
Backend consistently returns:
```json
{
  "success": true,
  "data": ...,
  "message": "...",
  "meta": { "current_page": ..., "last_page": ..., "per_page": ..., "total": ... }
}
```

Frontend `BaseCrudService.getAll()` returns `data` (axios response.data) which is the full JSON object.  
`useCrud` extracts `rows` from `data.data ?? data.rows` and `meta` from `data.meta ?? data.pagination`. ✅

### 9.2 Error Handling
Backend uses Laravel validation which returns `422` with `{ "message": "...", "errors": { "field": ["..."] } }`.  
Frontend `apiClient` normalizes errors to `{ message, errors, status }`.  
`useFormSubmit` maps validation errors via `toFormErrors()`. ✅

### 9.3 Authentication
Backend uses Bearer token auth.  
Frontend `apiClient` injects token from `localStorage('auth_token')`.  
401 response triggers redirect to `/login`. ✅

### 9.4 Soft Deletes
All 8 modules support soft deletes with `deleted_at` and `restore` endpoints.  
Frontend `DeleteDialog` + `RestoreDialog` used consistently across all list pages. ✅

### 9.5 Permission-Aware UI
All list pages use `usePermission().hasPermission()` to conditionally show:
- Create button
- Edit action
- Delete action
- Restore action
- Extra actions (activate/deactivate/reset-password for users)

Permission names follow convention: `{action}-{module}` (e.g., `create-site`, `update-barcode`, `delete-user`). ✅

### 9.6 Pagination
All list pages use `useCrud` with configurable `perPage` (default 15).  
Backend repositories accept `perPage` parameter and return `LengthAwarePaginator`.  
Frontend `Pagination` component renders from `meta`. ✅

### 9.7 Search
All list pages support search via `useCrud.setSearch()` with 400ms debounce.  
Backend repositories search across relevant fields (e.g., `site_id`, `site_name` for sites; `barcode_id`, `serial_number` for barcodes). ✅

### 9.8 Filtering
All list pages support filters via `FilterBar` component.  
Backend repositories accept `filters` array and apply conditional WHERE clauses. ✅

---

## 10. Summary

| Module | Backend Files | Frontend Service | Frontend Pages | Alignment |
|--------|--------------|-----------------|----------------|-----------|
| Sites | Controller, 2 FormRequests, Resource, Repository, Service, Interface | `BaseCrudService('/sites')` | List, Form | ✅ |
| Material Types | Controller, 2 FormRequests, Resource, Repository, Service, Interface | `BaseCrudService('/material-types')` | List, Form | ✅ |
| Material Models | Controller, 2 FormRequests, Resource, Repository, Service, Interface | `MaterialModelService` (extends Base) | List, Form | ✅ |
| Materials | Controller, 2 FormRequests, Resource, Repository, Service, Interface | `BaseCrudService('/materials')` | List, Form | ✅ |
| Permissions | Controller, 2 FormRequests, Resource, Repository, Service, Interface | `BaseCrudService('/permissions')` | List, Form | ✅ |
| Roles | Controller, 2 FormRequests, Resource, Repository, Service, Interface | `BaseCrudService('/roles')` | List, Form | ✅ |
| Users | Controller, 2 FormRequests, Resource, Repository, Service, Interface | `UserService` (extends Base) | List, Form | ✅ |
| Barcodes | Controller, 2 FormRequests, 3 Resources, Repository, Service, Interface | `BarcodeService` (extends Base) | List, Form, Detail | ✅ |

### Overall Verdict: ✅ **ALL MODULES FULLY ALIGNED**

No discrepancies found between frontend and backend contracts. All field names, validation rules, API endpoints, response shapes, and permission checks are consistent across the stack.
