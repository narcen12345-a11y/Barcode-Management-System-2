# PHASE 2 - TASK 2.12

# Judul

Create Barcode Histories Migration

---

# Tujuan

Membuat migration tabel `barcode_histories`.

Tabel ini digunakan untuk menyimpan seluruh riwayat perubahan data barcode sehingga setiap perubahan informasi dapat dilacak tanpa mengubah identitas barcode.

Setiap perubahan yang dilakukan user WAJIB tercatat pada tabel ini.

Task ini hanya membuat migration tabel `barcode_histories`.

---

# Dokumen Referensi

WAJIB membaca:

- docs/PROJECT_STATUS.md
- docs/AI_CONTEXT.md
- docs/DATABASE_FINAL.md
- docs/DATABASE_DESIGN.md
- docs/ERD.md
- docs/MIGRATION_PLAN.md
- docs/BARCODE_DATABASE_SPEC.md
- docs/BARCODE_RULES.md
- docs/HISTORY_RULES.md
- docs/BUSINESS_RULES.md

---

# Yang Boleh Dikerjakan

- Membuat migration tabel barcode_histories.
- Menggunakan Laravel 12.
- Membuat seluruh Foreign Key sesuai spesifikasi.
- Menggunakan timestamps().
- Menggunakan index yang diperlukan.

---

# Struktur Minimal

Kolom minimal:

- id
- barcode_id (FK ke barcodes)
- field_name
- old_value
- new_value
- changed_by (FK ke users)
- change_reason (nullable)
- created_at
- updated_at

---

# Penjelasan Kolom

## barcode_id

Mengacu ke barcode yang diubah.

Satu barcode dapat memiliki banyak history.

---

## field_name

Nama field yang berubah.

Contoh:

- site
- material
- type
- model
- serial_number
- status

---

## old_value

Nilai sebelum diubah.

---

## new_value

Nilai setelah diubah.

---

## changed_by

User yang melakukan perubahan.

---

## change_reason

Opsional.

Digunakan jika user memberikan alasan perubahan.

---

# Ketentuan

History tidak boleh dihapus secara otomatis ketika barcode masih memiliki histori.

Gunakan strategi Foreign Key yang aman.

History hanya boleh ditambahkan.

History tidak boleh diubah kembali setelah dibuat.

---

# Yang Tidak Boleh

JANGAN:

- Membuat migration lain.
- Mengubah migration sebelumnya.
- Membuat model.
- Membuat service.
- Membuat repository.
- Membuat controller.
- Membuat API.
- Membuat seeder.
- Membuat factory.

---

# Acceptance Criteria

- Hanya migration barcode_histories.
- Foreign Key benar.
- Index sesuai kebutuhan.
- Migration dapat dijalankan.
- Tidak ada migration lain dibuat.

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