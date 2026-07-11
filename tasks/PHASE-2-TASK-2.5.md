# PHASE 2 - TASK 2.5

# Judul

Create Role User Pivot Migration

---

# Tujuan

Membuat migration tabel `role_user` sebagai pivot antara tabel users dan roles.

Task ini hanya membuat migration pivot `role_user`.

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
- PERMISSION_MATRIX.md

---

# Yang Boleh Dikerjakan

- Membuat migration tabel `role_user`.
- Menambahkan Foreign Key ke tabel users.
- Menambahkan Foreign Key ke tabel roles.
- Menambahkan Primary Key atau Composite Unique sesuai praktik terbaik Laravel.
- Menambahkan created_at dan updated_at.

---

# Struktur Minimal

Tabel `role_user` minimal memiliki:

- id
- user_id
- role_id
- created_at
- updated_at

Ketentuan:

- user_id mengacu ke tabel users.
- role_id mengacu ke tabel roles.
- Kombinasi user_id + role_id tidak boleh duplikat (gunakan unique composite index).

---

# Yang Tidak Boleh Dikerjakan

JANGAN:

- Membuat migration permission_role.
- Membuat model.
- Membuat seeder.
- Membuat factory.
- Mengubah migration users.
- Mengubah migration roles.
- Mengubah migration permissions.

---

# Acceptance Criteria

- Hanya migration role_user yang dibuat.
- Foreign Key berfungsi dengan benar.
- Composite Unique Index diterapkan.
- Migration dapat dijalankan tanpa error.

---

# Output Yang Harus Dilaporkan

AI wajib melaporkan:

- Nama migration.
- Struktur kolom.
- Foreign Key yang dibuat.
- Index yang dibuat.
- File yang dibuat.
- File yang diubah.
- Verifikasi.
- Konfirmasi bahwa task lain belum dikerjakan.