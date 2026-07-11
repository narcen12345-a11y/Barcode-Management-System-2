# UI SPECIFICATION - MASTER MATERIAL

## Tujuan

Halaman ini digunakan untuk mengelola seluruh Master Material yang digunakan pada Barcode Management System.

Hanya Admin dan Super Admin yang memiliki akses.

---

# Layout Halaman

Urutan tampilan:

1. Header
2. Statistik
3. Filter
4. Tabel Material
5. Form Tambah/Edit Material
6. Pagination

---

# Header

Menampilkan:

- Judul Halaman
- Breadcrumb

Contoh:

Dashboard / Master Material

---

# Statistik

Tampilkan Card:

- Total Material
- Total Type
- Total Model
- Material Aktif
- Material Nonaktif

---

# Filter

Field:

- Material Name
- Type
- Model
- Status

Button:

- Search
- Reset

---

# Tabel Material

Kolom:

- No
- Material Name
- Type
- Model
- Status
- Dibuat Oleh
- Tanggal Dibuat
- Action

---

# Action

Button:

- Detail
- Edit
- Nonaktifkan / Aktifkan

Material yang sudah digunakan oleh Barcode tidak boleh dihapus permanen.

---

# Form Tambah Material

Field:

- Material Name
- Type
- Model
- Status

Button:

- Save
- Cancel

---

# Validasi

- Material Name wajib diisi.
- Type wajib dipilih.
- Model wajib dipilih.
- Kombinasi Material, Type, dan Model tidak boleh duplikat.

---

# Konfirmasi

Jika user menekan Cancel setelah mengubah data:

"Perubahan belum disimpan."

Pilihan:

- Lanjut Edit
- Keluar Tanpa Menyimpan

---

# Detail Material

Menampilkan:

- Informasi Material
- Type
- Model
- Jumlah Barcode yang menggunakan Material
- Tanggal Dibuat
- Terakhir Diubah

---

# Empty State

Apabila belum ada Material:

"Belum ada data Material."

---

# Responsive

Desktop:

Tabel penuh.

Tablet:

Tabel responsif.

Mobile:

Card Layout.

---

# UI Style

Gunakan:

- Shadcn UI
- Tailwind CSS
- Lucide Icons

---

# Performa

- Server Side Pagination.
- Search realtime.
- Dropdown Type dan Model menggunakan Search.
- Mendukung ribuan data tanpa penurunan performa.