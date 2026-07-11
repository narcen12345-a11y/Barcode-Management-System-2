# FILTER RULES

## Tujuan

Dokumen ini mengatur seluruh aturan bisnis mengenai fitur Filter dan Pencarian pada Barcode Management System.

---

## Rule 001 - Multi Filter

Sistem harus mendukung penggunaan lebih dari satu filter secara bersamaan.

---

## Rule 002 - Filter Site

User dapat memfilter berdasarkan:

- Site ID
- Site Name

---

## Rule 003 - Filter Material

User dapat memfilter berdasarkan:

- Material Name

---

## Rule 004 - Filter Type

User dapat memfilter berdasarkan:

- Type

---

## Rule 005 - Filter Model

User dapat memfilter berdasarkan:

- Model

---

## Rule 006 - Filter Status

User dapat memfilter berdasarkan:

- NEW (MOS)
- OLD (DISMANTLE)

---

## Rule 007 - Filter Serial Number

User dapat mencari Barcode menggunakan Serial Number secara langsung.

---

## Rule 008 - Filter Barcode ID

User dapat mencari Barcode menggunakan Barcode ID.

---

## Rule 009 - Filter Tanggal

User dapat memfilter berdasarkan:

- Tanggal Pembuatan
- Tanggal Perubahan

Filter harus mendukung rentang tanggal (Date Range).

---

## Rule 010 - Kombinasi Filter

Semua filter dapat digunakan secara bersamaan.

Contoh:

- Site ID
- Material
- Type
- Model
- Status

untuk mendapatkan hasil yang lebih spesifik.

---

## Rule 011 - Pencarian Cepat

Setiap daftar data harus memiliki kotak pencarian (Search Box).

Pencarian dilakukan secara real-time tanpa perlu me-refresh halaman.

---

## Rule 012 - Reset Filter

Sistem menyediakan tombol **Reset Filter** untuk mengembalikan seluruh filter ke kondisi awal.

---

## Rule 013 - Penyimpanan Filter

Sistem mengingat filter terakhir yang digunakan selama sesi login masih aktif.

---

## Rule 014 - Hasil Tidak Ditemukan

Apabila tidak ada data yang sesuai dengan filter, tampilkan pesan:

"Tidak ada data yang sesuai dengan filter yang dipilih."

---

## Rule 015 - Hak Akses

Filter hanya menampilkan data yang memang berhak diakses oleh user sesuai Role dan Permission.