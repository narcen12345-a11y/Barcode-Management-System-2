# PERMISSION MATRIX

## Tujuan

Dokumen ini mendefinisikan hak akses setiap Role pada Barcode Management System.

Role yang digunakan:

- Super Admin
- Admin
- User

---

# Dashboard

| Fitur | Super Admin | Admin | User |
|--------|-------------|-------|------|
| Melihat Dashboard | ✅ | ✅ | ✅ |

---

# Barcode

| Fitur | Super Admin | Admin | User |
|--------|-------------|-------|------|
| Lihat Barcode | ✅ | ✅ | ✅ |
| Detail Barcode | ✅ | ✅ | ✅ |
| Generate Barcode | ✅ | ✅ | ✅ |
| Edit Barcode | ✅ | ✅ | ✅ |
| Print Barcode | ✅ | ✅ | ✅ |
| Scan Barcode | ✅ | ✅ | ✅ |
| Lihat History | ✅ | ✅ | ✅ |

---

# Master Site

| Fitur | Super Admin | Admin | User |
|--------|-------------|-------|------|
| Lihat Site | ✅ | ✅ | ❌ |
| Tambah Site | ✅ | ✅ | ❌ |
| Edit Site | ✅ | ✅ | ❌ |
| Nonaktifkan Site | ✅ | ✅ | ❌ |

---

# Master Material

| Fitur | Super Admin | Admin | User |
|--------|-------------|-------|------|
| Lihat Material | ✅ | ✅ | ❌ |
| Tambah Material | ✅ | ✅ | ❌ |
| Edit Material | ✅ | ✅ | ❌ |
| Nonaktifkan Material | ✅ | ✅ | ❌ |

---

# User Management

| Fitur | Super Admin | Admin | User |
|--------|-------------|-------|------|
| Lihat User | ✅ | ✅ | ❌ |
| Tambah User | ✅ | ✅ | ❌ |
| Verifikasi User | ✅ | ✅ | ❌ |
| Aktif / Nonaktif User | ✅ | ✅ | ❌ |
| Reset Password User | ✅ | ✅ | ❌ |
| Ubah Role | ✅ | ❌ | ❌ |

---

# Audit Log

| Fitur | Super Admin | Admin | User |
|--------|-------------|-------|------|
| Lihat Audit Log | ✅ | ✅ | ❌ |

---

# Activity Log

| Fitur | Super Admin | Admin | User |
|--------|-------------|-------|------|
| Lihat Activity Log | ✅ | ✅ | ❌ |

---

# Spreadsheet

| Fitur | Super Admin | Admin | User |
|--------|-------------|-------|------|
| Lihat Spreadsheet | ✅ | ✅ | ✅ |
| Export Spreadsheet | ✅ | ✅ | ✅ |

---

# Pengaturan Sistem

| Fitur | Super Admin | Admin | User |
|--------|-------------|-------|------|
| Konfigurasi Sistem | ✅ | ❌ | ❌ |

---

# Catatan

- Seluruh hak akses wajib diperiksa pada Backend menggunakan Middleware dan Policy.
- Frontend wajib menyembunyikan menu dan tombol yang tidak dimiliki oleh Role pengguna.
- Seluruh perubahan Role dan Permission wajib dicatat pada Audit Log.