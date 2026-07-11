# UI SPECIFICATION - USER MANAGEMENT

## Tujuan

Halaman ini digunakan untuk mengelola seluruh akun pengguna pada Barcode Management System.

Hanya Admin dan Super Admin yang memiliki akses.

---

# Layout Halaman

Urutan tampilan:

1. Header
2. Statistik
3. Filter
4. Tabel User
5. Detail User
6. Form Tambah/Edit User
7. Pagination

---

# Header

Menampilkan:

- Judul Halaman
- Breadcrumb

Contoh:

Dashboard / User Management

---

# Statistik

Tampilkan Card:

- Total User
- User Aktif
- Menunggu Verifikasi
- User Nonaktif
- Super Admin
- Admin
- User

---

# Filter

Field:

- Nama
- Username
- Email
- Role
- Status

Button:

- Search
- Reset

---

# Tabel User

Kolom:

- No
- Nama
- Username
- Email
- Role
- Status
- Terakhir Login
- Action

---

# Action

Button:

- Detail
- Edit
- Verifikasi
- Aktifkan
- Nonaktifkan
- Reset Password

Hanya Super Admin yang dapat mengubah Role.

---

# Form Tambah User

Field:

- Nama Lengkap
- Username
- Email
- Nomor Telepon (Opsional)
- Role
- Status

Password dibuat otomatis oleh sistem.

Admin dapat memilih:

- Kirim Password melalui Email
atau
- Salin Password

---

# Detail User

Menampilkan:

- Informasi User
- Role
- Status
- Tanggal Dibuat
- Terakhir Login
- Jumlah Barcode yang dibuat
- Jumlah Barcode yang diedit

---

# Validasi

- Username harus unik.
- Email harus unik.
- Nama wajib diisi.
- Role wajib dipilih.

---

# Konfirmasi

Saat menonaktifkan akun:

"Apakah Anda yakin ingin menonaktifkan akun ini?"

Pilihan:

- Ya
- Batal

---

# Empty State

Apabila belum ada User:

"Belum ada data pengguna."

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
- Filter tanpa refresh halaman.

---

# Hak Akses

Super Admin:

- Hak penuh.

Admin:

- Tidak dapat mengubah Role Super Admin.
- Tidak dapat menghapus Super Admin.

User:

- Tidak memiliki akses ke halaman ini.