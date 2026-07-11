# HISTORY RULES

## Tujuan

Dokumen ini mengatur seluruh aturan bisnis mengenai riwayat perubahan (History) pada setiap Barcode.

---

## Rule 001 - History Otomatis

Setiap perubahan data Barcode wajib membuat History baru secara otomatis.

User tidak dapat membuat History secara manual.

---

## Rule 002 - Data Yang Dicatat

History minimal mencatat:

- Barcode ID
- Tanggal & Waktu Perubahan
- User yang melakukan perubahan
- Data sebelum perubahan
- Data setelah perubahan
- Jenis perubahan

---

## Rule 003 - Jenis Perubahan

Jenis perubahan yang dicatat antara lain:

- Create
- Update
- Status Change
- Restore
- Soft Delete

---

## Rule 004 - Barcode ID

Seluruh History selalu mengacu pada Barcode ID yang sama.

History tidak boleh berpindah ke Barcode lain.

---

## Rule 005 - Waktu Perubahan

Sistem menggunakan waktu server sebagai waktu resmi pencatatan History.

---

## Rule 006 - User Perubahan

Setiap perubahan harus menyimpan informasi:

- User ID
- Nama User
- Role User

---

## Rule 007 - Riwayat Tidak Dapat Diubah

History bersifat permanen.

Tidak boleh diedit maupun dihapus oleh User maupun Admin.

---

## Rule 008 - Tampilan History

Pada halaman Detail Barcode tersedia menu History yang menampilkan seluruh perubahan secara kronologis, dari yang terbaru hingga yang paling lama.

---

## Rule 009 - Filter History

History dapat difilter berdasarkan:

- Barcode ID
- Serial Number
- User
- Jenis Perubahan
- Rentang Tanggal

---

## Rule 010 - Audit

History menjadi salah satu sumber utama untuk proses audit dan pelacakan perubahan data.