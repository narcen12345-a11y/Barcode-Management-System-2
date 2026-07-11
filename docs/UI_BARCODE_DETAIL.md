# UI SPECIFICATION - BARCODE DETAIL

## Tujuan

Halaman ini digunakan untuk menampilkan seluruh informasi Barcode secara lengkap beserta riwayat perubahan dan aksi yang dapat dilakukan oleh pengguna.

---

# Layout Halaman

Urutan tampilan:

1. Header
2. Informasi Barcode
3. Barcode Code 128
4. Informasi Material
5. Riwayat Perubahan
6. Action Button

---

# Header

Menampilkan:

- Judul Halaman
- Breadcrumb

Contoh:

Dashboard / Barcode / Detail

---

# Informasi Barcode

Tampilkan:

- Barcode ID
- Status
- Tanggal Dibuat
- Terakhir Diubah
- Dibuat Oleh
- Terakhir Diubah Oleh

---

# Barcode Code 128

Tampilkan:

- Barcode Code 128 ukuran besar
- Nilai Barcode ID di bawah barcode

Button:

- Print Barcode
- Download Barcode

---

# Informasi Material

Tampilkan:

- Site ID
- Site Name
- Material Name
- Type
- Model
- Serial Number
- Status

Seluruh field bersifat Read Only.

---

# Riwayat Perubahan

Tampilkan dalam bentuk tabel.

Kolom:

- Tanggal
- User
- Jenis Perubahan
- Ringkasan Perubahan

Button:

Lihat Detail

---

# Action Button

Button:

- Edit Barcode
- Print Barcode
- Kembali

Hak akses mengikuti Role pengguna.

---

# Tampilan History

Urutkan dari perubahan terbaru ke perubahan paling lama.

Gunakan Pagination apabila data lebih dari 10.

---

# Empty State

Apabila belum ada riwayat perubahan:

Tampilkan pesan:

"Belum ada riwayat perubahan."

---

# Responsive

Desktop:

Layout dua kolom.

Mobile:

Layout satu kolom.

---

# UI Style

Gunakan:

- Shadcn UI
- Tailwind CSS
- Lucide Icons

---

# Performa

Data Barcode dan History dimuat secara terpisah agar halaman tetap cepat dibuka.