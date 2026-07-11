# PHASE 2.5 - TASK 2.15

# Judul

Design Complete Barcode Workflow

---

# Tujuan

Menyusun workflow lengkap aplikasi Barcode Management System berdasarkan seluruh dokumen yang telah dibuat.

Task ini TIDAK membuat source code.

Task ini HANYA mengisi dokumen:

docs/WORKFLOW_BARCODE.md

---

# Dokumen Referensi

WAJIB membaca seluruh dokumen berikut sebelum mulai bekerja.

Project

- docs/PROJECT_STATUS.md
- docs/AI_CONTEXT.md

Business Rules

- docs/BUSINESS_RULES.md
- docs/BARCODE_RULES.md
- docs/SITE_RULES.md
- docs/MATERIAL_RULES.md
- docs/HISTORY_RULES.md
- docs/SPREADSHEET_RULES.md
- docs/FILTER_RULES.md
- docs/AUTH_RULES.md
- docs/USER_RULES.md
- docs/VALIDATION_RULES.md

Database

- docs/DATABASE_FINAL.md
- docs/BARCODE_DATABASE_SPEC.md
- docs/ERD.md

UI

- docs/UI_LOGIN.md
- docs/UI_DASHBOARD.md
- docs/UI_BARCODE_LIST.md
- docs/UI_BARCODE_FORM.md
- docs/UI_BARCODE_DETAIL.md
- docs/UI_MASTER_SITE.md
- docs/UI_MASTER_MATERIAL.md
- docs/UI_USER_MANAGEMENT.md

---

# Yang Harus Dibuat

Isi file:

docs/WORKFLOW_BARCODE.md

---

# Isi Dokumen

Dokumen WAJIB menjelaskan workflow secara detail.

Minimal mencakup:

## 1 Login

- Login
- Validasi akun
- Akun belum diverifikasi
- Password salah
- Logout

---

## 2 Dashboard

- Data yang ditampilkan
- Shortcut menu
- Statistik

---

## 3 Generate Barcode

Workflow lengkap.

Mulai klik tombol Generate Barcode.

Sampai barcode berhasil dibuat.

---

## 4 Form Barcode

Urutan:

Site

↓

Material

↓

Type

↓

Model

↓

Serial Number

↓

Status

↓

Generate Code128

↓

Save

↓

Cancel

---

## 5 Edit Barcode

Workflow lengkap.

Yang berubah hanyalah informasi.

Barcode ID tetap.

Barcode fisik tetap.

History tercatat.

Spreadsheet ikut berubah.

---

## 6 Scan Code128

Workflow scan.

Barcode ditemukan.

Barcode tidak ditemukan.

---

## 7 Search

Pencarian.

Barcode ID.

Serial Number.

Site.

Material.

---

## 8 Filter

Filter Site.

Filter Material.

Filter Type.

Filter Model.

Filter Status.

Gabungan seluruh filter.

---

## 9 Detail Barcode

Informasi lengkap.

History.

Timeline.

Code128.

---

## 10 Spreadsheet

Pencatatan otomatis.

Update otomatis.

Link Barcode.

---

## 11 Audit Log

Kapan dibuat.

Kapan diubah.

Siapa yang mengubah.

---

## 12 Activity Log

Login.

Logout.

Generate.

Edit.

Export.

Import.

---

## 13 Error Handling

Data tidak ditemukan.

SN duplikat.

Site tidak ada.

Material tidak ada.

Akses ditolak.

---

## 14 Hak Akses

Admin.

Master.

User.

---

## 15 Workflow Diagram

Gunakan diagram ASCII.

Contoh:

Login

↓

Dashboard

↓

Generate Barcode

↓

Isi Form

↓

Generate Code128

↓

Save

↓

Spreadsheet

↓

History

---

# Ketentuan

JANGAN membuat source code.

JANGAN membuat API.

JANGAN membuat Controller.

JANGAN membuat Model.

JANGAN membuat Repository.

JANGAN membuat Service.

JANGAN membuat Migration.

Hanya mengisi WORKFLOW_BARCODE.md.

---

# Acceptance Criteria

Workflow harus dapat dipahami bahkan oleh programmer baru yang belum mengenal proyek ini.

Dokumen harus menjadi acuan implementasi backend maupun frontend.

---

# Output

Laporkan:

1. Bagian yang dibuat.
2. Ringkasan isi.
3. File yang diubah.
4. Verifikasi.
5. Konfirmasi task lain belum dikerjakan.