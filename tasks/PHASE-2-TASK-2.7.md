# PHASE 2 - TASK 2.7

# Judul

Create Material Types Migration

---

# Tujuan

Membuat migration tabel `material_types`.

Task ini hanya membuat migration tabel `material_types`.

Tidak boleh membuat migration tabel lain.

---

# Dokumen Referensi

WAJIB membaca:

- DATABASE_FINAL.md
- DATABASE_DESIGN.md
- ERD.md
- MIGRATION_PLAN.md
- MATERIAL_RULES.md

---

# Yang Boleh Dikerjakan

- Membuat migration tabel `material_types`.
- Menggunakan standar Laravel 12.
- Menambahkan Primary Key.
- created_at.
- updated_at.
- softDeletes() jika diperlukan sesuai dokumen referensi.

---

# Struktur Minimal

Tabel `material_types` minimal memiliki:

- id
- name (unique)
- description (nullable)
- is_active (default true)
- created_at
- updated_at
- deleted_at (jika menggunakan Soft Delete)

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
- Mengubah migration yang sudah ada.

---

# Acceptance Criteria

- Hanya migration `material_types` yang dibuat.
- Kolom mengikuti struktur minimal.
- Migration dapat dijalankan tanpa error.
- Tidak ada tabel lain yang dibuat.

---

# Output Yang Harus Dilaporkan

AI wajib melaporkan:

1. Nama migration.
2. Struktur kolom.
3. File yang dibuat.
4. File yang diubah.
5. Verifikasi.
6. Konfirmasi bahwa task lain belum dikerjakan.