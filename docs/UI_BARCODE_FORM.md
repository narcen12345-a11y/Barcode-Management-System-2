# UI SPECIFICATION - BARCODE FORM

## Tujuan

Halaman ini digunakan untuk membuat Barcode baru serta mengubah informasi Barcode yang sudah ada tanpa mengubah Barcode ID.

---

# Layout Halaman

Urutan tampilan:

1. Header
2. Preview Barcode
3. Form Informasi
4. Barcode Code 128
5. Action Button

---

# Header

Menampilkan:

- Judul Halaman
- Breadcrumb

Contoh:

Dashboard / Barcode / Create

atau

Dashboard / Barcode / Edit

---

# Preview Barcode

Bagian paling atas menampilkan:

Barcode ID

Status

Tanggal Dibuat

Tanggal Terakhir Diubah

---

# Form Informasi

## Site

Field:

Site ID

Menggunakan Dropdown + Search.

Site Name

Otomatis mengikuti Site ID yang dipilih.

Apabila tidak tersedia:

None / Site Not Detected

---

## Material

Field:

Material Name

Dropdown + Search.

---

## Type

Dropdown + Search.

Otomatis mengikuti Material apabila tersedia.

Masih dapat diubah apabila diperlukan.

---

## Model

Dropdown + Search.

Otomatis mengikuti Material apabila tersedia.

Masih dapat diubah apabila diperlukan.

---

## Serial Number

Input Manual.

Tombol:

Scan Code 128

Apabila hasil scan berhasil, Serial Number langsung terisi.

---

## Status

Pilihan:

NEW (MOS)

OLD (DISMANTLE)

Default:

NEW (MOS)

---

# Preview Barcode Code 128

Setelah Barcode berhasil disimpan, tampilkan:

Barcode Code 128

Beserta nilai Barcode ID di bawahnya.

---

# Tombol

Save

Cancel

Reset

Print Barcode

---

# Konfirmasi Cancel

Apabila user telah mengubah data lalu menekan Cancel, tampilkan dialog:

"Perubahan belum disimpan. Apakah Anda yakin ingin keluar?"

Pilihan:

- Lanjut Edit
- Keluar Tanpa Menyimpan

---

# Validasi

Field wajib:

- Site
- Material
- Type
- Model
- Serial Number
- Status

Apabila ada yang kosong:

Field diberi tanda merah.

---

# Edit Barcode

Saat membuka halaman Edit:

Seluruh data sebelumnya harus otomatis terisi.

Barcode ID tidak dapat diubah.

---

# Responsive

Desktop:

Layout 2 kolom.

Mobile:

Layout 1 kolom.

---

# UI Style

Gunakan:

- Shadcn UI
- Tailwind CSS
- Lucide Icons

---

# Performa

Dropdown menggunakan Search.

Data dimuat secara bertahap (Lazy Loading) apabila jumlah data sangat banyak.