# UI SPECIFICATION - BARCODE LIST

## Tujuan

Halaman ini digunakan untuk melihat, mencari, memfilter, mengedit, dan membuka detail seluruh Barcode.

Halaman ini merupakan halaman utama setelah Dashboard.

---

# Layout Halaman

Urutan dari atas ke bawah:

1. Header
2. Quick Action
3. Filter
4. Tabel Barcode
5. Pagination

---

# Header

Menampilkan:

- Judul Halaman
- Total Data
- Breadcrumb

Contoh:

Dashboard / Barcode

---

# Quick Action

Button:

+ Generate Barcode

Export Spreadsheet

Refresh

Hak akses mengikuti Role.

---

# Filter

Filter dapat digabungkan.

Field:

- Site ID
- Site Name
- Material
- Type
- Model
- Status
- Serial Number
- Barcode ID
- Tanggal Dibuat

Button:

Search

Reset

---

# Tabel Barcode

Kolom:

- No
- Barcode ID
- Barcode Code 128 (Preview)
- Site ID
- Site Name
- Material
- Type
- Model
- Serial Number
- Status
- Last Update
- Action

---

# Action

Button:

View

Edit

History

Print Barcode

---

# Klik Baris

Klik salah satu baris membuka:

Detail Barcode

---

# Pagination

Pilihan:

10

25

50

100

Data per halaman.

---

# Empty State

Apabila tidak ada data:

Tampilkan ilustrasi.

Pesan:

Belum ada Barcode.

---

# Loading

Gunakan Skeleton Loading.

---

# Responsive

Desktop:

Tabel penuh.

Tablet:

Tabel menyesuaikan.

Mobile:

Menggunakan Card Layout.

---

# UI Style

Gunakan:

- Shadcn UI
- Tailwind CSS
- Lucide Icons

---

# Performa

Filter harus merespons dengan cepat.

Pencarian menggunakan Server Side Pagination.

---

# Hak Akses

User hanya melihat Barcode yang memiliki hak akses.

Admin melihat seluruh Barcode.

Super Admin melihat seluruh Barcode tanpa batasan.