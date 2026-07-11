# PHASE 2 - TASK 2.6

# Judul

Create Permission Role Pivot Migration

---

# Tujuan

Membuat migration tabel `permission_role` sebagai pivot antara tabel roles dan permissions.

Task ini hanya membuat migration pivot `permission_role`.

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

- Membuat migration tabel `permission_role`.
- Menambahkan Foreign Key ke tabel roles.
- Menambahkan Foreign Key ke tabel permissions.
- Menambahkan Primary Key.
- Menambahkan created_at dan updated_at.
- Menambahkan Composite Unique Index pada kombinasi role_id dan permission_id.

---

# Struktur Minimal

Tabel `permission_role` minimal memiliki:

- id
- role_id
- permission_id
- created_at
- updated_at

Ketentuan:

- role_id mengacu ke tabel roles.
- permission_id mengacu ke tabel permissions.
- Kombinasi role_id + permission_id tidak boleh duplikat.

---

# Yang Tidak Boleh Dikerjakan

JANGAN:

- Membuat migration tabel lain.
- Membuat model.
- Membuat seeder.
- Membuat factory.
- Membuat controller.
- Membuat repository.
- Membuat service.
- Mengubah migration users.
- Mengubah migration roles.
- Mengubah migration permissions.
- Mengubah migration role_user.

---

# Acceptance Criteria

- Hanya migration permission_role yang dibuat.
- Foreign Key berfungsi dengan benar.
- Composite Unique Index diterapkan.
- Migration dapat dijalankan tanpa error.
- Tidak ada tabel lain yang dibuat.

---

# Output Yang Harus Dilaporkan

AI wajib melaporkan:

1. Nama migration.
2. Struktur kolom.
3. Foreign Key.
4. Index.
5. File yang dibuat.
6. File yang diubah.
7. Verifikasi.
8. Konfirmasi bahwa task lain belum dikerjakan.