# USER RULES

## Tujuan

Dokumen ini mengatur seluruh aturan bisnis mengenai Role, Permission, dan hak akses pengguna pada Barcode Management System.

---

## Rule 001 - Role

Sistem memiliki minimal Role berikut:

- Super Admin
- Admin
- User

---

## Rule 002 - Super Admin

Super Admin memiliki hak penuh terhadap seluruh sistem.

Termasuk:

- Mengelola seluruh User
- Mengubah Role
- Mengelola Permission
- Mengakses seluruh Site
- Mengakses seluruh Barcode
- Mengakses seluruh Audit Log
- Mengelola Master Data
- Mengubah konfigurasi sistem

---

## Rule 003 - Admin

Admin memiliki hak untuk:

- Membuat akun User
- Memverifikasi akun
- Mengaktifkan akun
- Menonaktifkan akun
- Mengelola Master Site
- Mengelola Master Material
- Membuat Barcode
- Mengedit Barcode
- Melihat Barcode
- Mengakses Audit Log
- Mengakses Dashboard

Admin tidak dapat mengubah hak akses Super Admin.

---

## Rule 004 - User

User memiliki hak untuk:

- Login
- Logout
- Membuat Barcode
- Mengedit Barcode yang diizinkan
- Melihat Barcode yang diizinkan
- Menggunakan fitur Scan Barcode
- Menggunakan Filter
- Melihat History Barcode
- Mengubah Password sendiri

User tidak dapat:

- Membuat akun baru
- Memverifikasi akun
- Mengubah Master Site
- Mengubah Master Material
- Mengakses Audit Log
- Mengubah Role

---

## Rule 005 - Permission

Setiap fitur dikendalikan oleh Permission.

Role merupakan kumpulan dari beberapa Permission.

---

## Rule 006 - Pemeriksaan Hak Akses

Setiap halaman, API, dan aksi harus memeriksa Permission sebelum dijalankan.

---

## Rule 007 - Menu Dinamis

Menu yang ditampilkan kepada pengguna harus mengikuti Role dan Permission yang dimiliki.

User tidak boleh melihat menu yang tidak memiliki hak akses.

---

## Rule 008 - Perubahan Role

Perubahan Role hanya dapat dilakukan oleh Super Admin.

---

## Rule 009 - Perubahan Permission

Perubahan Permission hanya dapat dilakukan oleh Super Admin.

---

## Rule 010 - Audit Log

Perubahan berikut wajib dicatat:

- Perubahan Role
- Perubahan Permission
- Perubahan Hak Akses
- Penambahan User
- Penghapusan User
- Aktivasi User
- Penonaktifan User