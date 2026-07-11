# PHASE 2 - TASK 2.2

# Judul

Create Users Migration

---

# Tujuan

Membuat migration tabel Users sebagai fondasi Authentication System.

Task ini hanya membuat migration Users.

Tidak boleh membuat migration tabel lain.

---

# Dokumen Referensi

WAJIB membaca:

- DATABASE_FINAL.md
- DATABASE_DESIGN.md
- ERD.md
- MIGRATION_PLAN.md
- AUTH_RULES.md
- USER_RULES.md
- VALIDATION_RULES.md

---

# Yang Boleh Dikerjakan

- Membuat migration tabel users.
- Menggunakan standar Laravel 12.
- Menambahkan Primary Key.
- created_at.
- updated_at.
- softDeletes() apabila telah ditentukan pada DATABASE_FINAL.md.
- Foreign Key hanya jika memang dibutuhkan untuk tabel users.

---

# Yang Tidak Boleh Dikerjakan

JANGAN:

- Membuat migration Roles.
- Membuat migration Permissions.
- Membuat Seeder.
- Membuat Factory.
- Membuat Model.
- Membuat Controller.
- Membuat API.
- Membuat Repository.
- Membuat Service.
- Mengubah file lain di luar migration users.

---

# Acceptance Criteria

Migration hanya membuat tabel users.

Migration mengikuti seluruh Business Rules.

Migration dapat dijalankan tanpa error.

Tidak ada migration lain yang dibuat.

---

# Output Yang Harus Dilaporkan

AI wajib melaporkan:

- Nama migration.
- Struktur kolom.
- File yang dibuat.
- File yang diubah.
- Verifikasi.
- Konfirmasi bahwa task lain belum dikerjakan.