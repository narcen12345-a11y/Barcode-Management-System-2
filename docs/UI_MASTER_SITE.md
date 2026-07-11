# UI SPECIFICATION - MASTER SITE

## Tujuan

Halaman ini digunakan untuk mengelola seluruh Master Site yang tersedia pada Barcode Management System.

Hanya Admin dan Super Admin yang memiliki akses.

---

# Layout Halaman

Urutan tampilan:

1. Header
2. Statistik
3. Filter
4. Tabel Site
5. Form Tambah/Edit Site
6. Pagination

---

# Header

Menampilkan:

- Judul Halaman
- Breadcrumb

Contoh:

Dashboard / Master Site

---

# Statistik

Tampilkan Card:

- Total Site
- Site Aktif
- Site Nonaktif

---

# Filter

Field:

- Site ID
- Site Name
- Status

Button:

- Search
- Reset

---

# Tabel Site

Kolom:

- No
- Site ID
- Site Name
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

Site yang sudah digunakan oleh Barcode tidak boleh dihapus permanen.

---

# Form Tambah Site

Field:

- Site ID
- Site Name
- Status

Button:

- Save
- Cancel

---

# Validasi

- Site ID wajib diisi.
- Site Name wajib diisi.
- Site ID harus unik.
- Site Name tidak boleh kosong.

---

# Konfirmasi

Jika user menekan Cancel setelah mengubah data:

"Perubahan belum disimpan."

Pilihan:

- Lanjut Edit
- Keluar Tanpa Menyimpan

---

# Empty State

Apabila belum ada Site:

"Belum ada data Site."

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

Pencarian menggunakan Server Side Pagination.

Dropdown Search harus responsif meskipun jumlah Site sangat banyak.