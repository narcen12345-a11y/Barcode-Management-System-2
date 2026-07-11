# SITE RULES

## Tujuan

Dokumen ini mengatur seluruh aturan bisnis mengenai Master Site yang digunakan pada Barcode Management System.

---

## Rule 001 - Master Site

Semua Site yang digunakan dalam aplikasi harus berasal dari Master Site.

User tidak diperbolehkan mengetik Site secara bebas.

---

## Rule 002 - Site ID

Site ID bersifat unik.

Tidak boleh ada dua Site dengan Site ID yang sama.

---

## Rule 003 - Site Name

Site Name mengikuti Master Site.

Site Name otomatis mengikuti Site ID yang dipilih.

User tidak dapat mengubah Site Name secara manual apabila Site ID berasal dari Master Site.

---

## Rule 004 - Site Tidak Ditemukan

Apabila material tidak memiliki riwayat Site sebelumnya, user dapat memilih:

None / Site Not Detected

---

## Rule 005 - Penambahan Site Baru

Hanya Admin yang dapat menambahkan Site baru ke Master Site.

User biasa tidak memiliki hak untuk menambahkan Site baru.

---

## Rule 006 - Pengeditan Site

Perubahan informasi Site hanya dapat dilakukan oleh Admin.

Perubahan Site akan langsung tersedia pada daftar pilihan seluruh user.

---

## Rule 007 - Penghapusan Site

Site tidak boleh dihapus secara permanen apabila pernah digunakan oleh Barcode.

Gunakan status Nonaktif apabila Site sudah tidak digunakan.

---

## Rule 008 - Pencarian Site

Pemilihan Site harus mendukung pencarian berdasarkan:

- Site ID
- Site Name

---

## Rule 009 - Konsistensi Data

Site ID dan Site Name harus selalu berpasangan sesuai Master Site.

Tidak boleh terjadi kombinasi Site ID dan Site Name yang tidak sesuai.

---

## Rule 010 - Audit Log

Setiap penambahan, perubahan, atau penonaktifan Site wajib dicatat pada Audit Log.