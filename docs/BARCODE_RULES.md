# BARCODE RULES

## Tujuan

Dokumen ini mengatur seluruh aturan bisnis mengenai Barcode pada Barcode Management System.

---

## Rule 001 - Barcode ID

Setiap Barcode memiliki Barcode ID yang unik.

Barcode ID dibuat oleh sistem dan tidak dapat diubah.

---

## Rule 002 - Generate Barcode

Setiap material hanya memiliki satu Barcode.

Barcode dibuat satu kali dan digunakan selama material masih terdaftar di sistem.

---

## Rule 003 - Barcode Permanen

Barcode tidak perlu dicetak ulang apabila informasi material berubah.

Barcode tetap sama selama Barcode ID tidak berubah.

---

## Rule 004 - Informasi Barcode

Setiap Barcode minimal memiliki informasi berikut:

- Barcode ID
- Site ID
- Site Name
- Material Name
- Type
- Model
- Serial Number (SN)
- Status
- Tanggal Dibuat
- Tanggal Diperbarui

---

## Rule 005 - Serial Number

Serial Number wajib diisi.

Serial Number harus unik.

Tidak boleh terdapat dua Barcode dengan Serial Number yang sama.

---

## Rule 006 - Scan Code 128

Sistem harus mendukung scan Barcode Code 128 untuk mengisi Serial Number.

Apabila hasil scan berhasil, Serial Number akan terisi otomatis.

---

## Rule 007 - Barcode Code 128

Setelah Barcode disimpan, sistem akan menghasilkan Barcode Code 128 berdasarkan Barcode ID.

Barcode Code 128 ditampilkan pada halaman detail Barcode.

---

## Rule 008 - Edit Barcode

User diperbolehkan mengubah informasi Barcode tanpa membuat Barcode baru.

Barcode ID tetap sama.

Barcode Code 128 tetap sama.

---

## Rule 009 - Riwayat Perubahan

Setiap perubahan informasi Barcode wajib dicatat pada Barcode History.

---

## Rule 010 - Audit Log

Seluruh aktivitas berikut wajib dicatat pada Audit Log:

- Membuat Barcode
- Mengubah Barcode
- Mengubah Status
- Menghapus (Soft Delete)
- Restore

---

## Rule 011 - Status Barcode

Status yang tersedia:

- NEW (MOS)
- OLD (DISMANTLE)

Status dapat diubah sesuai kondisi material.

---

## Rule 012 - Soft Delete

Barcode tidak boleh dihapus secara permanen.

Gunakan Soft Delete apabila Barcode tidak lagi digunakan.

---

## Rule 013 - Validasi

Sistem wajib menolak penyimpanan apabila:

- Serial Number kosong.
- Material belum dipilih.
- Status belum dipilih.

---

## Rule 014 - Konsistensi Data

Perubahan Site, Material, Type, atau Model harus langsung tercermin pada informasi Barcode tanpa mengubah Barcode ID.

---

## Rule 015 - Hak Akses

User hanya dapat mengubah Barcode sesuai hak akses yang diberikan.

Admin memiliki hak penuh terhadap seluruh Barcode.