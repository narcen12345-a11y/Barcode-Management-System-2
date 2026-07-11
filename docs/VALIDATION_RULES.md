# VALIDATION RULES

## Tujuan

Dokumen ini mengatur seluruh aturan validasi data pada Barcode Management System.

---

## Rule 001 - Data Wajib

Data berikut wajib diisi sebelum Barcode dapat disimpan:

- Site ID
- Site Name
- Material Name
- Type
- Model
- Serial Number (SN)
- Status

---

## Rule 002 - Site

Site hanya boleh dipilih dari Master Site.

Apabila Site tidak tersedia, user harus memilih:

None / Site Not Detected

---

## Rule 003 - Material

Material hanya boleh dipilih dari Master Material.

---

## Rule 004 - Type

Type hanya boleh dipilih dari daftar Type yang tersedia.

---

## Rule 005 - Model

Model hanya boleh dipilih dari daftar Model yang tersedia.

---

## Rule 006 - Serial Number

Serial Number wajib diisi.

Serial Number harus unik.

Serial Number tidak boleh mengandung spasi di awal maupun di akhir.

---

## Rule 007 - Status

Status hanya boleh memiliki salah satu nilai berikut:

- NEW (MOS)
- OLD (DISMANTLE)

---

## Rule 008 - Barcode

Barcode ID dibuat otomatis oleh sistem.

User tidak dapat mengubah Barcode ID.

---

## Rule 009 - Simpan Data

Apabila terdapat satu saja data yang tidak valid, proses penyimpanan dibatalkan.

Sistem harus menampilkan pesan kesalahan yang jelas kepada pengguna.

---

## Rule 010 - Edit Data

Seluruh validasi pada proses pembuatan Barcode juga berlaku saat proses Edit Barcode.

---

## Rule 011 - Scan Barcode

Apabila hasil scan tidak valid atau tidak dapat dibaca, sistem harus menampilkan pesan kesalahan dan meminta pengguna melakukan scan ulang atau mengisi Serial Number secara manual.

---

## Rule 012 - Konsistensi Data

Perubahan Site, Material, Type, atau Model tidak boleh menyebabkan data menjadi tidak konsisten dengan Master Data.

---

## Rule 013 - Validasi Duplikasi

Sistem harus memeriksa kemungkinan duplikasi:

- Barcode ID
- Serial Number

Sebelum data disimpan.

---

## Rule 014 - Audit Log

Setiap kegagalan validasi yang menyebabkan proses penyimpanan dibatalkan harus dicatat pada Audit Log apabila berasal dari proses sistem atau integrasi.