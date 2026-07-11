# AUDIT LOG RULES

## Tujuan

Dokumen ini mengatur seluruh aturan bisnis mengenai Audit Log pada Barcode Management System.

---

## Rule 001 - Audit Log Otomatis

Setiap aktivitas penting wajib dicatat secara otomatis oleh sistem.

User tidak dapat membuat Audit Log secara manual.

---

## Rule 002 - Aktivitas Yang Dicatat

Minimal aktivitas berikut wajib dicatat:

- Login
- Logout
- Gagal Login
- Membuat Barcode
- Mengubah Barcode
- Mengubah Status Barcode
- Soft Delete Barcode
- Restore Barcode
- Menambah User
- Mengubah User
- Approval User
- Menambah Site
- Mengubah Site
- Menambah Material
- Mengubah Material

---

## Rule 003 - Informasi Yang Dicatat

Setiap Audit Log minimal menyimpan:

- Audit Log ID
- User ID
- Nama User
- Role User
- Jenis Aktivitas
- Nama Modul
- Barcode ID (jika ada)
- Alamat IP
- Browser / Device
- Tanggal & Waktu
- Status (Success / Failed)

---

## Rule 004 - Audit Log Permanen

Audit Log tidak boleh diedit maupun dihapus oleh User.

---

## Rule 005 - Hak Akses

Hanya Admin yang dapat melihat Audit Log.

User biasa tidak memiliki akses.

---

## Rule 006 - Filter Audit Log

Audit Log dapat difilter berdasarkan:

- User
- Aktivitas
- Modul
- Barcode ID
- Rentang Tanggal
- Status

---

## Rule 007 - Pencarian Audit Log

Audit Log mendukung pencarian berdasarkan:

- Nama User
- Barcode ID
- Jenis Aktivitas

---

## Rule 008 - Urutan Data

Audit Log ditampilkan dari aktivitas terbaru ke aktivitas paling lama.

---

## Rule 009 - Kegagalan Sistem

Apabila terjadi kegagalan sinkronisasi, error sistem, atau proses penting gagal dijalankan, sistem wajib mencatatnya ke Audit Log.

---

## Rule 010 - Retensi Data

Audit Log tidak boleh dihapus secara otomatis tanpa kebijakan retensi yang ditentukan oleh Admin.