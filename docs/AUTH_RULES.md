# AUTH RULES

## Tujuan

Dokumen ini mengatur seluruh aturan bisnis mengenai Authentication dan User Management pada Barcode Management System.

---

## Rule 001 - Login

User wajib login menggunakan Username atau Email dan Password.

---

## Rule 002 - Pembuatan Akun

User tidak dapat mendaftarkan akun sendiri.

Seluruh akun hanya dapat dibuat oleh Admin.

---

## Rule 003 - Verifikasi Akun

Setiap akun baru berstatus **Pending Verification**.

Akun belum dapat digunakan sebelum diverifikasi oleh Admin.

---

## Rule 004 - Status Akun

Status akun terdiri dari:

- Pending Verification
- Active
- Inactive
- Suspended

---

## Rule 005 - Aktivasi Akun

Hanya Admin yang dapat mengubah status akun menjadi Active.

---

## Rule 006 - Login Akun Belum Aktif

Akun dengan status Pending Verification, Inactive, atau Suspended tidak dapat login.

Sistem menampilkan pesan yang sesuai dengan status akun.

---

## Rule 007 - Hak Akses

Hak akses ditentukan berdasarkan Role dan Permission.

---

## Rule 008 - Perubahan Password

User dapat mengganti password miliknya sendiri setelah berhasil login.

Password lama wajib diverifikasi sebelum password baru disimpan.

---

## Rule 009 - Reset Password

Hanya Admin yang dapat melakukan reset password pengguna.

Password hasil reset harus diganti oleh user saat login pertama setelah reset.

---

## Rule 010 - Session Login

Sistem akan mengakhiri sesi login secara otomatis apabila tidak ada aktivitas selama periode yang ditentukan.

---

## Rule 011 - Gagal Login

Setiap percobaan login yang gagal wajib dicatat pada Audit Log.

---

## Rule 012 - Logout

Logout mengakhiri sesi pengguna dan menghapus token autentikasi yang aktif.

---

## Rule 013 - Penghapusan Akun

Akun yang pernah memiliki aktivitas pada sistem tidak boleh dihapus secara permanen.

Gunakan status Inactive atau Soft Delete.

---

## Rule 014 - Audit Log

Seluruh aktivitas berikut wajib dicatat:

- Login
- Logout
- Gagal Login
- Pembuatan Akun
- Verifikasi Akun
- Aktivasi Akun
- Reset Password
- Perubahan Password
- Perubahan Role
- Penonaktifan Akun