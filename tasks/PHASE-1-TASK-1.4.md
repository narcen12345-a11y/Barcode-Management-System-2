# PHASE 1 - TASK 1.4
# Database Design Documentation

## Tujuan

Menyusun dokumentasi desain database berdasarkan PRD sebelum implementasi migration dan model.

Dokumentasi ini akan menjadi acuan utama untuk seluruh pengembangan backend.

---

## Yang harus dilakukan

Baca seluruh folder:

- .ai
- docs

Kemudian buat file berikut.

docs/DATABASE_DESIGN.md

Dokumen harus berisi minimal:

# 1. Gambaran Umum Database

Jelaskan tujuan database pada aplikasi.

---

# 2. Daftar Entitas

Minimal identifikasi entitas berikut:

- Users
- Roles
- Permissions
- Sites
- Materials
- Material Types
- Material Models
- Barcodes
- Barcode Histories
- Audit Logs
- Activity Logs

Apabila terdapat entitas lain yang diperlukan berdasarkan PRD, tambahkan dengan penjelasan singkat.

---

# 3. Relasi Antar Entitas

Jelaskan hubungan antar entitas dalam bentuk teks.

Contoh:

Users memiliki banyak Barcode.

Site memiliki banyak Material.

Material memiliki satu Type.

Material memiliki satu Model.

Barcode memiliki banyak History.

---

# 4. Prinsip Database

Tuliskan aturan utama seperti:

- Soft Delete digunakan jika diperlukan.
- Timestamp wajib.
- Barcode ID bersifat permanen.
- Serial Number unik.
- Foreign Key wajib.
- Hindari duplikasi data.

---

# Yang TIDAK BOLEH dilakukan

Jangan membuat migration.

Jangan membuat model.

Jangan membuat database.

Jangan mengubah konfigurasi.

Jangan membuat API.

---

## Setelah selesai

Berikan laporan:

- File yang dibuat.
- Ringkasan isi dokumen.
- Pastikan tidak ada perubahan pada source code.

Kemudian berhenti.

## Definition of Done

DATABASE_DESIGN.md selesai dibuat.

Tidak ada perubahan pada source code.

Tidak ada migration baru.