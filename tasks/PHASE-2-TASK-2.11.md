# PHASE 2 - TASK 2.11

# Judul

Create Barcodes Migration

---

# Tujuan

Membuat migration tabel `barcodes`.

Tabel ini adalah tabel utama aplikasi Barcode Management System.

Seluruh barcode yang dibuat oleh user akan disimpan pada tabel ini.

Task ini hanya membuat migration tabel `barcodes`.

---

# Dokumen Referensi

WAJIB membaca:

- docs/PROJECT_STATUS.md
- docs/AI_CONTEXT.md
- docs/DATABASE_FINAL.md
- docs/DATABASE_DESIGN.md
- docs/ERD.md
- docs/MIGRATION_PLAN.md
- docs/BARCODE_RULES.md
- docs/BARCODE_DATABASE_SPEC.md
- docs/SPREADSHEET_RULES.md

---

# Yang Boleh Dikerjakan

- Membuat migration tabel barcodes.
- Menggunakan Laravel 12.
- Membuat seluruh Foreign Key sesuai BARCODE_DATABASE_SPEC.
- Menggunakan timestamps().
- Menggunakan softDeletes().
- Menggunakan index yang diperlukan.

---

# Struktur

Ikuti BARCODE_DATABASE_SPEC.md.

JANGAN membuat struktur berdasarkan asumsi.

Apabila terdapat perbedaan antara DATABASE_FINAL.md dan BARCODE_DATABASE_SPEC.md, gunakan BARCODE_DATABASE_SPEC.md sebagai acuan utama.

---

# Ketentuan

Barcode harus memiliki ID permanen.

Barcode tidak boleh berubah meskipun:

- Site berubah.
- Material berubah.
- Status berubah.
- Serial Number berubah.

Barcode harus tetap menjadi identitas utama.

Seluruh relasi harus mengikuti spesifikasi.

---

# Yang Tidak Boleh

JANGAN:

- Membuat barcode_histories.
- Membuat audit_logs.
- Membuat activity_logs.
- Membuat model.
- Membuat seeder.
- Membuat factory.
- Membuat controller.
- Membuat repository.
- Membuat service.

---

# Acceptance Criteria

- Hanya migration barcodes.
- Foreign Key sesuai spesifikasi.
- Index sesuai spesifikasi.
- Soft Delete.
- Migration dapat dijalankan.

---

# Output

Laporkan:

1. Nama migration.
2. Struktur kolom lengkap.
3. Foreign Key.
4. Index.
5. File dibuat.
6. File diubah.
7. Verifikasi.
8. Konfirmasi task lain belum dikerjakan.