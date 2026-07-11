# PHASE 2 - TASK 2.14

# Judul

Create Activity Logs Migration

---

# Tujuan

Membuat migration tabel `activity_logs`.

Tabel ini digunakan untuk mencatat seluruh aktivitas pengguna selama menggunakan aplikasi.

Berbeda dengan audit_logs yang mencatat perubahan data, activity_logs mencatat aktivitas penggunaan sistem.

Task ini hanya membuat migration tabel `activity_logs`.

---

# Dokumen Referensi

WAJIB membaca:

- docs/PROJECT_STATUS.md
- docs/AI_CONTEXT.md
- docs/DATABASE_FINAL.md
- docs/DATABASE_DESIGN.md
- docs/ERD.md
- docs/MIGRATION_PLAN.md
- docs/BUSINESS_RULES.md
- docs/AUTH_RULES.md
- docs/USER_RULES.md

---

# Yang Boleh Dikerjakan

- Membuat migration activity_logs.
- Menggunakan Laravel 12.
- Menggunakan Foreign Key sesuai spesifikasi.
- Menambahkan index yang diperlukan.

---

# Struktur Minimal

Kolom minimal:

- id
- user_id
- activity
- module
- description
- ip_address
- user_agent
- session_id
- created_at

---

# Penjelasan Kolom

## user_id

User yang melakukan aktivitas.

Foreign Key ke users.

Nullable.

Gunakan nullOnDelete().

---

## activity

Nama aktivitas.

Contoh:

- login
- logout
- generate_barcode
- edit_barcode
- delete_barcode
- export_spreadsheet
- import_master_site
- import_master_material
- scan_barcode
- search_barcode

---

## module

Nama modul.

Contoh:

- Authentication
- Barcode
- Site
- Material
- User
- Spreadsheet

---

## description

Penjelasan singkat aktivitas.

Contoh:

"User berhasil login."

"Barcode BR000012 berhasil dibuat."

"Barcode BR000045 berhasil diperbarui."

---

## ip_address

Alamat IP user.

Nullable.

---

## user_agent

Browser atau Device.

Nullable.

---

## session_id

Session Laravel.

Nullable.

---

# Ketentuan

Activity Log bersifat IMMUTABLE.

Tidak boleh diubah.

Tidak boleh dihapus.

Tidak menggunakan softDeletes().

Tidak menggunakan updated_at.

Hanya menggunakan created_at.

---

# Index

Minimal:

- user_id
- activity
- module
- created_at

---

# Yang Tidak Boleh

JANGAN:

- Membuat migration lain.
- Mengubah migration sebelumnya.
- Membuat model.
- Membuat repository.
- Membuat service.
- Membuat controller.
- Membuat API.
- Membuat seeder.
- Membuat factory.

---

# Acceptance Criteria

- Hanya migration activity_logs.
- Tidak menggunakan updated_at.
- Tidak menggunakan softDeletes().
- Foreign Key sesuai spesifikasi.
- Index sesuai spesifikasi.
- Migration dapat dijalankan.

---

# Output

AI wajib melaporkan:

1. Nama migration.
2. Struktur kolom lengkap.
3. Foreign Key.
4. Index.
5. File dibuat.
6. File diubah.
7. Verifikasi.
8. Konfirmasi task lain belum dikerjakan.