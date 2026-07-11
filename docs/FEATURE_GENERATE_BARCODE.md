# FEATURE SPECIFICATION - GENERATE BARCODE

## Tujuan

Fitur ini digunakan untuk membuat Barcode baru beserta seluruh informasi material dan mencatatnya ke dalam sistem.

---

# Alur Proses

1. User membuka menu Generate Barcode.
2. Sistem membuat Barcode ID secara otomatis.
3. Sistem menampilkan Form Barcode.
4. User mengisi seluruh informasi yang diperlukan.
5. User menekan tombol Save.
6. Sistem melakukan validasi seluruh data.
7. Apabila validasi berhasil:
   - Data disimpan ke tabel `barcodes`.
   - Riwayat awal dibuat pada `barcode_histories`.
   - Audit Log dicatat.
   - Spreadsheet diperbarui.
   - Barcode Code 128 dibuat.
8. User diarahkan ke halaman Detail Barcode.

---

# Data Yang Diisi

- Site ID
- Site Name
- Material Name
- Type
- Model
- Serial Number
- Status

---

# Barcode ID

- Dibuat otomatis oleh sistem.
- Bersifat permanen.
- Tidak dapat diubah oleh pengguna.

---

# Validasi

- Semua field wajib diisi.
- Serial Number harus unik.
- Site harus berasal dari Master Site atau memilih "None / Site Not Detected".
- Material harus berasal dari Master Material.

---

# Barcode Code 128

Setelah data berhasil disimpan, sistem menghasilkan Barcode Code 128 berdasarkan Barcode ID.

Barcode tersebut ditampilkan pada halaman Detail Barcode dan dapat dicetak.

---

# Spreadsheet

Setelah proses berhasil:

- Sistem memperbarui Spreadsheet.
- Menambahkan atau memperbarui data Barcode.
- Menyimpan URL menuju halaman Detail Barcode.

---

# History

Sistem membuat riwayat pertama dengan jenis perubahan:

Create

---

# Audit Log

Sistem mencatat:

- User
- Waktu
- Barcode ID
- Aktivitas: Generate Barcode
- Status: Success

---

# Error Handling

Apabila proses gagal:

- Data tidak disimpan.
- Spreadsheet tidak diperbarui.
- Barcode tidak dibuat.
- Audit Log mencatat status Failed.
- Tampilkan pesan kesalahan kepada pengguna.