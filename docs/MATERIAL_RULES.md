# MATERIAL RULES

## Tujuan

Dokumen ini mengatur seluruh aturan bisnis mengenai Master Material yang digunakan pada Barcode Management System.

---

## Rule 001 - Master Material

Semua Material yang digunakan dalam aplikasi harus berasal dari Master Material.

User tidak diperbolehkan mengetik nama Material secara manual.

---

## Rule 002 - Material Name

Nama Material harus unik.

Tidak boleh terdapat dua Material dengan nama yang sama.

---

## Rule 003 - Type

Type dipilih berdasarkan daftar Type yang tersedia pada Master Data.

---

## Rule 004 - Model

Model dipilih berdasarkan daftar Model yang tersedia pada Master Data.

---

## Rule 005 - Hubungan Material

Setiap Material memiliki:

- 1 Type
- 1 Model

---

## Rule 006 - Material Baru

Apabila Material belum tersedia pada Master Material, user tidak dapat membuatnya sendiri.

User harus mengajukan kepada Admin.

Admin dapat menambahkan Material baru melalui menu Master Material.

---

## Rule 007 - Type Baru

Penambahan Type hanya dapat dilakukan oleh Admin.

---

## Rule 008 - Model Baru

Penambahan Model hanya dapat dilakukan oleh Admin.

---

## Rule 009 - Pengeditan Master Material

Perubahan informasi Material hanya dapat dilakukan oleh Admin.

Perubahan tersebut akan langsung tersedia pada seluruh user.

---

## Rule 010 - Penghapusan Material

Material yang sudah pernah digunakan oleh Barcode tidak boleh dihapus secara permanen.

Gunakan status Nonaktif apabila Material sudah tidak digunakan.

---

## Rule 011 - Pencarian Material

Pemilihan Material harus mendukung pencarian berdasarkan:

- Nama Material
- Type
- Model

---

## Rule 012 - Konsistensi Data

Type dan Model harus sesuai dengan Material yang dipilih.

Tidak boleh terjadi kombinasi Material, Type, dan Model yang tidak sesuai.

---

## Rule 013 - Audit Log

Setiap penambahan, perubahan, maupun penonaktifan Material wajib dicatat pada Audit Log.