# PHASE 3

# MODULE AUTHENTICATION

## Tujuan

Mengimplementasikan seluruh backend Authentication Module berdasarkan seluruh dokumen proyek.

Module ini harus selesai secara end-to-end.

---

# WAJIB membaca

Project

- docs/PROJECT_STATUS.md
- docs/AI_CONTEXT.md

Business Rules

- docs/BUSINESS_RULES.md
- docs/AUTH_RULES.md
- docs/USER_RULES.md
- docs/VALIDATION_RULES.md

Workflow

- docs/WORKFLOW_BARCODE.md

Database

- docs/DATABASE_FINAL.md
- docs/ERD.md

Migration

- seluruh migration authentication

---

# Yang Harus Dibuat

## DTO

LoginRequestDTO

RegisterUserDTO

VerifyUserDTO

ChangePasswordDTO

---

## Enum

UserStatusEnum

RoleEnum

PermissionEnum

---

## Form Request

LoginRequest

CreateUserRequest

UpdateUserRequest

VerifyUserRequest

ChangePasswordRequest

---

## Model

User

Role

Permission

---

## Repository

Interface

Repository

---

## Service

AuthenticationService

UserService

RoleService

PermissionService

---

## API Resource

UserResource

RoleResource

PermissionResource

---

## Controller

AuthController

UserController

RoleController

PermissionController

---

## API

POST /login

POST /logout

POST /verify-user

POST /change-password

GET /me

CRUD User

CRUD Role

CRUD Permission

---

# Yang Tidak Boleh

Jangan mengerjakan:

Barcode

Site

Material

Spreadsheet

Audit

Activity Log

---

# Acceptance Criteria

Authentication Module selesai sepenuhnya.

Tidak mengerjakan module lain.

---

# Output

Laporkan:

1. File dibuat.
2. File diubah.
3. Struktur folder.
4. Ringkasan implementasi.
5. Verifikasi.
6. Konfirmasi module lain belum dikerjakan.