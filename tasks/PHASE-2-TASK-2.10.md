# PHASE 2 - TASK 2.10

# Judul

Create Sites Migration

---

# Tujuan

Membuat migration tabel `sites`.

Tabel ini digunakan sebagai Master Site yang dapat dipilih saat pembuatan Barcode.

Task ini hanya membuat migration tabel sites.

---

# Dokumen Referensi

WAJIB membaca:

- docs/PROJECT_STATUS.md
- docs/AI_CONTEXT.md
- docs/DATABASE_FINAL.md
- docs/DATABASE_DESIGN.md
- docs/ERD.md
- docs/MIGRATION_PLAN.md
- docs/SITE_RULES.md

---

# Yang Boleh Dikerjakan

- Membuat migration tabel sites.
- Menggunakan Laravel 12.
- Menambahkan timestamps().
- Menambahkan softDeletes() apabila diperlukan.

---

# Struktur Minimal

Kolom minimal:

- id
- site_id
- site_name
- region (nullable)
- address (nullable)
- latitude (nullable)
- longitude (nullable)
- is_active
- created_at
- updated_at
- deleted_at

---

# Ketentuan

- site_id wajib UNIQUE.
- site_name tidak boleh kosong.
- is_active default true.
- Site dapat digunakan oleh banyak Barcode.

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

- Hanya migration sites.
- site_id unique.
- Laravel 12 standard.
- Migration dapat dijalankan.

---

# Output

Laporkan:

1. Nama migration.
2. Struktur kolom.
3. Index.
4. File dibuat.
5. File diubah.
6. Verifikasi.
7. Konfirmasi task lain belum dikerjakan.