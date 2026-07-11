# DATABASE FINAL

## Tujuan

Dokumen ini mendefinisikan struktur database final yang akan digunakan pada Barcode Management System.

Seluruh migration, model, repository, service, dan API wajib mengacu pada dokumen ini.

---

# Daftar Tabel

## Core System

1. users
2. roles
3. permissions
4. role_user
5. permission_role

---

## Master Data

6. sites
7. materials
8. material_types
9. material_models

---

## Barcode

10. barcodes
11. barcode_histories

---

## System

12. audit_logs
13. activity_logs

---

# Deskripsi Tabel

## users

Menyimpan data seluruh pengguna aplikasi.

---

## roles

Menyimpan daftar Role.

---

## permissions

Menyimpan daftar Permission.

---

## role_user

Relasi User dengan Role.

---

## permission_role

Relasi Role dengan Permission.

---

## sites

Master Site.

---

## materials

Master Material.

---

## material_types

Master Type.

---

## material_models

Master Model.

---

## barcodes

Menyimpan seluruh informasi Barcode.

---

## barcode_histories

Menyimpan seluruh riwayat perubahan Barcode.

---

## audit_logs

Menyimpan seluruh aktivitas penting sistem.

---

## activity_logs

Menyimpan aktivitas pengguna untuk kebutuhan monitoring.

---

# Standar Database

- Seluruh tabel menggunakan Primary Key.
- Seluruh relasi menggunakan Foreign Key.
- Seluruh tabel menggunakan created_at dan updated_at.
- Soft Delete diterapkan pada tabel yang memerlukan penghapusan logis.
- Barcode ID bersifat permanen.
- Serial Number bersifat unik.
- Seluruh relasi wajib menggunakan referential integrity.