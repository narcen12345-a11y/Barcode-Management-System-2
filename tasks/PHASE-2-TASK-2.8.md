# PHASE 2 - TASK 2.8

# Judul

Create Material Models Migration

---

# Tujuan

Membuat migration tabel `material_models`.

Task ini hanya membuat migration tabel `material_models`.

Tidak boleh membuat migration tabel lain.

---

# Dokumen Referensi

WAJIB membaca:

- docs/PROJECT_STATUS.md
- docs/AI_CONTEXT.md
- docs/DATABASE_FINAL.md
- docs/DATABASE_DESIGN.md
- docs/ERD.md
- docs/MIGRATION_PLAN.md
- docs/MATERIAL_RULES.md

---

# Yang Boleh Dikerjakan

- Membuat migration tabel material_models.
- Menggunakan standar Laravel 12.
- Menambahkan Primary Key.
- Menambahkan Foreign Key ke material_types.
- created_at.
- updated_at.
- softDeletes() apabila diperlukan.

---

# Struktur Minimal

Tabel material_models minimal memiliki:

- id
- material_type_id
- name
- description (nullable)
- is_active (default true)
- created_at
- updated_at
- deleted_at

Ketentuan:

- material_type_id wajib menjadi Foreign Key.
- Kombinasi material_type_id + name tidak boleh duplikat.

---

# Yang Tidak Boleh Dikerjakan

JANGAN:

- Membuat migration lain.
- Mengubah migration yang sudah ada.
- Membuat model.
- Membuat seeder.
- Membuat factory.
- Membuat controller.
- Membuat repository.
- Membuat service.

---

# Acceptance Criteria

- Hanya migration material_models yang dibuat.
- Foreign Key berfungsi.
- Composite Unique Index diterapkan pada material_type_id + name.
- Migration dapat dijalankan.
- Tidak ada migration lain yang dibuat.

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