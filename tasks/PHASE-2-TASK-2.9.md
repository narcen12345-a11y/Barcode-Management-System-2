# PHASE 2 - TASK 2.9

# Judul

Create Materials Migration

---

# Tujuan

Membuat migration tabel `materials`.

Tabel ini menjadi master seluruh material yang dapat dipilih saat pembuatan Barcode.

Task ini hanya membuat migration tabel `materials`.

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

- Membuat migration tabel materials.
- Menggunakan Laravel 12.
- Membuat Foreign Key ke material_types.
- Membuat Foreign Key ke material_models.
- Menambahkan timestamps().
- Menambahkan softDeletes() apabila diperlukan.

---

# Struktur Minimal

Kolom minimal:

- id
- material_type_id
- material_model_id
- material_code
- name
- description (nullable)
- is_active
- created_at
- updated_at
- deleted_at

---

# Ketentuan

- material_type_id wajib menjadi Foreign Key.
- material_model_id wajib menjadi Foreign Key.
- material_code harus UNIQUE.
- Kombinasi material_type_id + material_model_id + name tidak boleh duplikat.

---

# Yang Tidak Boleh Dikerjakan

JANGAN:

- Membuat migration lain.
- Mengubah migration sebelumnya.
- Membuat model.
- Membuat seeder.
- Membuat factory.
- Membuat controller.
- Membuat repository.
- Membuat service.

---

# Acceptance Criteria

- Hanya migration materials.
- Foreign Key benar.
- Unique material_code.
- Composite Unique Index diterapkan.
- Migration dapat dijalankan.

---

# Output

Laporkan:

1. Nama migration.
2. Struktur kolom.
3. Foreign Key.
4. Index.
5. File dibuat.
6. File diubah.
7. Verifikasi.
8. Konfirmasi task lain belum dikerjakan.