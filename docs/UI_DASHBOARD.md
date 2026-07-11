# UI SPECIFICATION - DASHBOARD

## Tujuan

Dashboard berfungsi sebagai pusat informasi utama setelah pengguna berhasil login.

Halaman harus menampilkan ringkasan kondisi data secara cepat tanpa pengguna harus membuka menu lain.

---

# Header

Menampilkan:

- Logo Aplikasi
- Nama Aplikasi
- Nama User
- Role User
- Tanggal & Jam Saat Ini
- Tombol Dark Mode
- Tombol Logout

---

# Statistik Utama

Tampilkan Card berikut:

1. Total Barcode

Jumlah seluruh Barcode.

---

2. Total Material

Jumlah seluruh Material.

---

3. Total Site

Jumlah seluruh Site.

---

4. Total User

Hanya tampil untuk Admin.

---

5. Barcode NEW (MOS)

Jumlah Barcode dengan status NEW.

---

6. Barcode OLD (DISMANTLE)

Jumlah Barcode dengan status OLD.

---

# Quick Action

Sediakan tombol cepat:

- Generate Barcode
- Scan Barcode
- Lihat Barcode
- Master Material
- Master Site

Menu mengikuti hak akses pengguna.

---

# Recent Activity

Menampilkan 10 aktivitas terakhir.

Kolom:

- Waktu
- User
- Aktivitas
- Barcode ID

---

# Recent Barcode

Menampilkan 10 Barcode terbaru.

Kolom:

- Barcode ID
- Site
- Material
- SN
- Status

Klik salah satu baris membuka Detail Barcode.

---

# Grafik

Tampilkan:

Barcode berdasarkan Status.

Barcode berdasarkan Site.

Barcode berdasarkan Material.

---

# Notifikasi

Menampilkan:

- Akun menunggu verifikasi.
- Material baru menunggu persetujuan.
- Sinkronisasi Spreadsheet gagal.
- Error sistem.

Hanya Admin yang dapat melihat seluruh notifikasi.

---

# Hak Akses

User hanya melihat data yang menjadi hak aksesnya.

Admin melihat seluruh data.

Super Admin melihat seluruh data beserta statistik sistem.

---

# Responsive

Dashboard harus optimal pada:

- Desktop
- Tablet
- Mobile

---

# UI Style

Gunakan:

- Shadcn UI
- Tailwind CSS
- Lucide Icons
- Card Layout
- Modern Dashboard
- Dark Mode Ready

---

# Performa

Dashboard harus memuat data utama dalam waktu kurang dari 3 detik pada penggunaan normal.