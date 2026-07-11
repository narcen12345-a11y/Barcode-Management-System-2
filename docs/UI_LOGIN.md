# UI SPECIFICATION - LOGIN

## Tujuan

Halaman Login digunakan sebagai gerbang utama untuk mengakses Barcode Management System.

---

# Layout

Tampilan dibagi menjadi dua bagian.

## Sebelah Kiri

Logo perusahaan.

Nama aplikasi:

Barcode Management System

Deskripsi singkat aplikasi.

Background menggunakan warna utama perusahaan.

---

## Sebelah Kanan

Form Login.

---

# Form Login

Field:

Username / Email

Password

Checkbox:

Remember Me

Button:

Login

Link:

Lupa Password (Opsional)

---

# Validasi

Username wajib diisi.

Password wajib diisi.

Apabila salah:

"Terdapat kesalahan Username atau Password."

---

# Login Berhasil

User diarahkan ke Dashboard sesuai Role.

---

# Login Gagal

Tetap berada pada halaman Login.

Password dikosongkan.

---

# Akun Belum Aktif

Tampilkan pesan:

"Akun Anda belum diverifikasi oleh Administrator."

---

# Akun Dinonaktifkan

Tampilkan pesan:

"Akun Anda sedang dinonaktifkan."

---

# Responsive

Desktop

Tablet

Mobile

Harus tetap nyaman digunakan.

---

# UI Style

Gunakan:

- Shadcn UI

- Tailwind CSS

- Icon Lucide

- Dark Mode Ready

---

# Keamanan

Password menggunakan tipe input password.

CSRF Protection.

Rate Limiting.

Session Authentication.