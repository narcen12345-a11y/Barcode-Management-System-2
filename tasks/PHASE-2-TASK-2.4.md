# PHASE 2 - TASK 2.4

# Judul

Create Permissions Migration

---

# Tujuan

Membuat migration tabel `permissions` sebagai dasar sistem Role-Based Access Control (RBAC).

Task ini hanya membuat migration tabel `permissions`.

Tidak boleh membuat migration tabel lain.

---

# Dokumen Referensi

WAJIB membaca:

- DATABASE_FINAL.md
- DATABASE_DESIGN.md
- ERD.md
- MIGRATION_PLAN.md
- USER_RULES.md
- PERMISSION_MATRIX.md

---

# Yang Boleh Dikerjakan

- Membuat migration tabel `permissions`.
- Menggunakan standar Laravel 12.
- Menambahkan Primary Key.
- created_at.
- updated_at.
- softDeletes() apabila diperlukan sesuai dokumen referensi.

---

# Struktur Minimal

Tabel `permissions` minimal memiliki:

- id
- name (unique)
- display_name
- module
- description (nullable)
- is_active (default true)
- created_at
- updated_at
- deleted_at (jika menggunakan Soft Delete)

---

# Yang Tidak Boleh Dikerjakan

JANGAN:

- Membuat pivot table.
- Membuat model.
- Membuat seeder.
- Membuat factory.
- Membuat controller.
- Membuat repository.
- Membuat service.
- Mengubah migration users.
- Mengubah migration roles.

---

# Acceptance Criteria

- Hanya migration `permissions` yang dibuat.
- Kolom mengikuti struktur minimal.
- Migration dapat dijalankan tanpa error.
- Tidak ada tabel lain yang dibuat.

---

# Output Yang Harus Dilaporkan

AI wajib melaporkan:

- Nama migration.
- Struktur kolom.
- File yang dibuat.
- File yang diubah.
- Verifikasi.
- Konfirmasi bahwa task lain belum dikerjakan.