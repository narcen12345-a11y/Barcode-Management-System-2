# PHASE 3 - TASK 3.2

# Judul

Implement Authentication Authorization & Permission

---

# Tujuan

Memperbaiki mekanisme Authorization pada Module Authentication berdasarkan hasil review.

Task ini HANYA berfokus pada hak akses (Authorization) dan Permission.

---

# Referensi Wajib

- docs/PROJECT_STATUS.md
- docs/AI_CONTEXT.md
- docs/AUTH_RULES.md
- docs/USER_RULES.md
- docs/VALIDATION_RULES.md
- docs/WORKFLOW_BARCODE.md
- docs/PERMISSION_MATRIX.md

Baca juga implementasi Module Authentication yang sudah ada.

---

# Temuan Review yang Harus Diperbaiki

- CRITICAL-01
- HIGH-02
- HIGH-03
- HIGH-04

---

# Yang Harus Dikerjakan

## 1. Route Protection

Pastikan seluruh endpoint selain login menggunakan middleware authentication yang sesuai.

---

## 2. Permission Middleware

Implementasikan pemeriksaan permission pada endpoint yang membutuhkan hak akses.

Contoh:

- create-user
- update-user
- delete-user
- verify-user
- reset-password
- manage-role
- manage-permission

Gunakan PermissionEnum sebagai acuan.

---

## 3. Role Restriction

Pastikan:

- Hanya Super Admin yang dapat mengelola Role.
- Hanya Super Admin yang dapat mengelola Permission.
- Admin tidak dapat mengubah Super Admin.

---

## 4. Controller

Pastikan Controller tidak berisi logika authorization.

Authorization dilakukan melalui middleware, policy, gate, atau mekanisme Laravel yang sesuai.

---

## 5. Service

Business rule tetap berada di Service.

---

# Yang Tidak Boleh

- Jangan membuat Barcode Module.
- Jangan membuat Site Module.
- Jangan membuat Material Module.
- Jangan membuat Spreadsheet Module.
- Jangan membuat Audit Log Module.
- Jangan membuat Activity Log Module.
- Jangan mengubah business rule yang sudah ada.

---

# Acceptance Criteria

- Endpoint sensitif terlindungi.
- Permission diterapkan sesuai PERMISSION_MATRIX.md.
- Role Restriction berjalan.
- Tidak ada hardcode permission string jika sudah tersedia PermissionEnum.

---

# Output

Laporkan:

1. File yang dibuat.
2. File yang diubah.
3. Ringkasan implementasi.
4. Verifikasi.
5. Konfirmasi task lain belum dikerjakan.