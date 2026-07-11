# BUSINESS RULES

## Tujuan

Dokumen ini merupakan indeks utama seluruh aturan bisnis pada aplikasi **Barcode Management System**.

Seluruh aturan bisnis dipecah ke dalam beberapa dokumen agar lebih mudah dipelihara, diperbarui, dan dipahami oleh tim pengembang maupun AI.

---

# Daftar Dokumen

## 1. Authentication Rules
File:
AUTH_RULES.md

Mengatur:

- Login
- Logout
- Session
- Approval User
- Aktivasi akun
- Verifikasi akun

---

## 2. User Rules
File:
USER_RULES.md

Mengatur:

- Role
- Permission
- Hak akses
- Pengelolaan user

---

## 3. Site Rules
File:
SITE_RULES.md

Mengatur:

- Master Site
- Site ID
- Site Name
- None / Site Not Detected

---

## 4. Material Rules
File:
MATERIAL_RULES.md

Mengatur:

- Master Material
- Type
- Model
- Penambahan material baru

---

## 5. Barcode Rules
File:
BARCODE_RULES.md

Mengatur seluruh siklus hidup Barcode.

---

## 6. Barcode History Rules
File:
HISTORY_RULES.md

Mengatur riwayat perubahan Barcode.

---

## 7. Spreadsheet Rules
File:
SPREADSHEET_RULES.md

Mengatur pencatatan otomatis ke Spreadsheet.

---

## 8. Filter Rules
File:
FILTER_RULES.md

Mengatur seluruh mekanisme pencarian data.

---

## 9. Audit Log Rules
File:
AUDIT_LOG_RULES.md

Mengatur pencatatan seluruh aktivitas sistem.

---

## 10. Dashboard Rules
File:
DASHBOARD_RULES.md

Mengatur dashboard dan statistik.

---

## 11. Validation Rules
File:
VALIDATION_RULES.md

Mengatur seluruh validasi input.

---

## 12. Security Rules
File:
SECURITY_RULES.md

Mengatur keamanan aplikasi.

---

## Catatan

Semua dokumen di atas merupakan sumber kebenaran (Single Source of Truth).

Implementasi database, backend, frontend, dan API wajib mengikuti aturan bisnis yang terdapat pada dokumen-dokumen tersebut.