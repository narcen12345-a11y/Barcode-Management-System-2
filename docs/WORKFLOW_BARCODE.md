# WORKFLOW BARCODE

## Tujuan

Dokumen ini menjelaskan workflow lengkap aplikasi Barcode Management System mulai dari Login hingga seluruh fitur yang tersedia.

Dokumen ini menjadi acuan implementasi backend maupun frontend.

---

## Daftar Isi

1. [Login](#1-login)
2. [Dashboard](#2-dashboard)
3. [Generate Barcode](#3-generate-barcode)
4. [Form Barcode](#4-form-barcode)
5. [Edit Barcode](#5-edit-barcode)
6. [Scan Code128](#6-scan-code128)
7. [Search](#7-search)
8. [Filter](#8-filter)
9. [Detail Barcode](#9-detail-barcode)
10. [Spreadsheet](#10-spreadsheet)
11. [Audit Log](#11-audit-log)
12. [Activity Log](#12-activity-log)
13. [Error Handling](#13-error-handling)
14. [Hak Akses](#14-hak-akses)
15. [Workflow Diagram](#15-workflow-diagram)

---

## 1. Login

### 1.1 Alur Login

```
User membuka halaman Login
        │
        ▼
Masukkan Username / Email dan Password
        │
        ▼
Sistem memeriksa CSRF Token
        │
        ├── CSRF Token tidak valid
        │       └── Tampilkan error: "Session expired, silakan refresh halaman."
        │
        ▼
Sistem memeriksa Rate Limiting
        │
        ├── Melebihi batas percobaan
        │       └── Tampilkan error: "Terlalu banyak percobaan login. Silakan coba lagi dalam X menit."
        │
        ▼
Sistem memvalidasi kredensial
        │
        ├── Username/Email tidak ditemukan
        │       └── Tampilkan: "Terdapat kesalahan Username atau Password."
        │           Password dikosongkan.
        │
        ├── Password salah
        │       └── Tampilkan: "Terdapat kesalahan Username atau Password."
        │           Password dikosongkan.
        │           Catat ke Audit Log: "Gagal Login"
        │
        ├── Akun berstatus "Pending Verification"
        │       └── Tampilkan: "Akun Anda belum diverifikasi oleh Administrator."
        │
        ├── Akun berstatus "Inactive"
        │       └── Tampilkan: "Akun Anda sedang dinonaktifkan."
        │
        ├── Akun berstatus "Suspended"
        │       └── Tampilkan: "Akun Anda sedang ditangguhkan."
        │
        └── Kredensial valid dan akun Active
                │
                ▼
        Sistem membuat Session / Token
                │
                ▼
        Catat ke Activity Log: "login"
                │
                ▼
        Redirect ke Dashboard sesuai Role
```

### 1.2 Logout

```
User menekan tombol Logout
        │
        ▼
Sistem menghapus session / token
        │
        ▼
Catat ke Activity Log: "logout"
        │
        ▼
Redirect ke halaman Login
```

### 1.3 Aturan

- User wajib login menggunakan Username atau Email dan Password.
- User tidak dapat mendaftarkan akun sendiri. Seluruh akun hanya dibuat oleh Admin.
- Setiap akun baru berstatus **Pending Verification** dan belum dapat digunakan sebelum diverifikasi Admin.
- Setiap percobaan login yang gagal dicatat pada Audit Log.
- Session berakhir otomatis apabila tidak ada aktivitas dalam periode tertentu.

---

## 2. Dashboard

### 2.1 Data yang Ditampilkan

Setelah login berhasil, user diarahkan ke Dashboard.

**Header:**
- Logo Aplikasi
- Nama Aplikasi: Barcode Management System
- Nama User
- Role User
- Tanggal & Jam Saat Ini
- Tombol Dark Mode
- Tombol Logout

**Statistik Utama (Card):**
- Total Barcode — jumlah seluruh Barcode
- Total Material — jumlah seluruh Material
- Total Site — jumlah seluruh Site
- Total User — hanya tampil untuk Admin / Super Admin
- Barcode NEW (MOS) — jumlah Barcode dengan status NEW
- Barcode OLD (DISMANTLE) — jumlah Barcode dengan status OLD

### 2.2 Shortcut Menu (Quick Action)

Tombol cepat yang ditampilkan sesuai hak akses:
- Generate Barcode
- Scan Barcode
- Lihat Barcode
- Master Material
- Master Site

### 2.3 Statistik

**Recent Activity:**
Menampilkan 10 aktivitas terakhir dengan kolom: Waktu, User, Aktivitas, Barcode ID.

**Recent Barcode:**
Menampilkan 10 Barcode terbaru dengan kolom: Barcode ID, Site, Material, SN, Status.
Klik salah satu baris membuka halaman Detail Barcode.

**Grafik:**
- Barcode berdasarkan Status
- Barcode berdasarkan Site
- Barcode berdasarkan Material

**Notifikasi (khusus Admin):**
- Akun menunggu verifikasi
- Material baru menunggu persetujuan
- Sinkronisasi Spreadsheet gagal
- Error sistem

### 2.4 Aturan

- User hanya melihat data yang menjadi hak aksesnya.
- Admin melihat seluruh data.
- Super Admin melihat seluruh data beserta statistik sistem.
- Dashboard harus memuat data utama dalam waktu kurang dari 3 detik.

---

## 3. Generate Barcode

### 3.1 Workflow Lengkap

```
User berada di halaman Barcode List
        │
        ▼
User menekan tombol "+ Generate Barcode"
        │
        ▼
Sistem memeriksa hak akses (Permission: create-barcode)
        │
        ├── Tidak memiliki akses
        │       └── Tampilkan: "Akses ditolak."
        │
        └── Memiliki akses
                │
                ▼
        Sistem menampilkan halaman Create Barcode (Form kosong)
                │
                ▼
        User mengisi Form Barcode (lihat bagian 4)
                │
                ▼
        User menekan tombol "Save"
                │
                ▼
        Sistem melakukan validasi (lihat bagian 4.8)
                │
                ├── Validasi gagal
                │       └── Tampilkan error pada field yang bermasalah
                │
                └── Validasi berhasil
                        │
                        ▼
                Sistem generate Barcode ID (format: BRC-YYYYMMDD-XXX)
                        │
                        ▼
                Sistem menyimpan data ke tabel barcodes
                        │
                        ▼
                Sistem membuat History (change_type: CREATE)
                        │
                        ▼
                Sistem mencatat ke Audit Log
                        │
                        ▼
                Sistem mencatat ke Activity Log: "generate_barcode"
                        │
                        ▼
                Sistem mencatat ke Spreadsheet (baris baru)
                        │
                        ▼
                Sistem generate Barcode Code 128
                        │
                        ▼
                Tampilkan halaman Detail Barcode
                        │
                        ▼
                User dapat mencetak atau mengunduh Barcode
```

### 3.2 Aturan Generate Barcode

- Setiap material hanya memiliki satu Barcode.
- Barcode ID bersifat permanen dan tidak berubah.
- Barcode Code 128 dihasilkan berdasarkan Barcode ID.
- Serial Number wajib diisi dan harus unik.
- Status default: NEW (MOS).
- Seluruh aktivitas dicatat ke History, Audit Log, Activity Log, dan Spreadsheet.

---

## 4. Form Barcode

### 4.1 Urutan Form

```
┌─────────────────────────────────────┐
│          PREVIEW BARCODE            │
│  Barcode ID: (otomatis setelah save)│
│  Status: NEW (MOS)                  │
│  Tanggal Dibuat: (otomatis)         │
│  Terakhir Diubah: (otomatis)        │
└─────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────┐
│  Site                               │
│  [Dropdown + Search]                │
│  Site Name: (otomatis)              │
│  Opsi: None / Site Not Detected     │
└─────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────┐
│  Material                           │
│  [Dropdown + Search]                │
└─────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────┐
│  Type                               │
│  [Dropdown + Search]                │
│  (Otomatis mengikuti Material)      │
│  (Masih dapat diubah)               │
└─────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────┐
│  Model                              │
│  [Dropdown + Search]                │
│  (Otomatis mengikuti Material)      │
│  (Masih dapat diubah)               │
└─────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────┐
│  Serial Number                      │
│  [Input Manual]  [Scan Code 128]    │
└─────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────┐
│  Status                             │
│  ○ NEW (MOS) (Default)              │
│  ○ OLD (DISMANTLE)                  │
└─────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────┐
│  Tombol Aksi                        │
│  [Save]  [Cancel]  [Reset]          │
└─────────────────────────────────────┘
```

### 4.2 Site

- Field: **Site ID** — Dropdown + Search.
- **Site Name** otomatis mengikuti Site ID yang dipilih.
- Apabila Site tidak tersedia, user memilih: **None / Site Not Detected**.
- Site hanya boleh dipilih dari Master Site.
- Pencarian Site berdasarkan: Site ID, Site Name.

### 4.3 Material

- Field: **Material Name** — Dropdown + Search.
- Material hanya boleh dipilih dari Master Material.
- Pencarian Material berdasarkan: Nama Material, Type, Model.

### 4.4 Type

- Dropdown + Search.
- Otomatis mengikuti Material yang dipilih (apabila Material memiliki Type tertentu).
- User masih dapat mengubah Type secara manual jika diperlukan.
- Type hanya boleh dipilih dari daftar Type yang tersedia.

### 4.5 Model

- Dropdown + Search.
- Otomatis mengikuti Material yang dipilih (apabila Material memiliki Model tertentu).
- User masih dapat mengubah Model secara manual jika diperlukan.
- Model hanya boleh dipilih dari daftar Model yang tersedia.

### 4.6 Serial Number

- Input Manual — user mengetik Serial Number.
- Tombol **Scan Code 128** — membuka dialog scan.
- Apabila hasil scan berhasil, Serial Number langsung terisi otomatis.
- Serial Number tidak boleh mengandung spasi di awal maupun di akhir.

### 4.7 Status

- Pilihan: **NEW (MOS)** atau **OLD (DISMANTLE)**.
- Default: **NEW (MOS)**.

### 4.8 Validasi

Sistem memeriksa sebelum menyimpan:

| Field | Aturan |
|-------|--------|
| Site | Wajib dipilih |
| Material | Wajib dipilih |
| Type | Wajib dipilih |
| Model | Wajib dipilih |
| Serial Number | Wajib diisi, unik, tanpa spasi di awal/akhir |
| Status | Wajib dipilih (NEW atau OLD) |

Apabila validasi gagal:
- Field yang bermasalah diberi tanda merah.
- Tampilkan pesan error yang jelas.
- Proses penyimpanan dibatalkan.

Apabila validasi berhasil:
- Data disimpan.
- History, Audit Log, Activity Log, dan Spreadsheet dicatat.

### 4.9 Tombol Aksi

| Tombol | Fungsi |
|--------|--------|
| Save | Menyimpan data setelah validasi |
| Cancel | Membatalkan, jika ada perubahan tampilkan konfirmasi |
| Reset | Mengembalikan form ke kondisi awal |
| Print Barcode | Mencetak Barcode (hanya setelah save) |

### 4.10 Konfirmasi Cancel

Apabila user telah mengubah data lalu menekan Cancel:
```
"Perubahan belum disimpan. Apakah Anda yakin ingin keluar?"
Pilihan:
- Lanjut Edit
- Keluar Tanpa Menyimpan
```

---

## 5. Edit Barcode

### 5.1 Workflow Lengkap

```
User berada di halaman Barcode List atau Detail Barcode
        │
        ▼
User menekan tombol "Edit"
        │
        ▼
Sistem memeriksa hak akses (Permission: edit-barcode)
        │
        ├── Tidak memiliki akses
        │       └── Tampilkan: "Akses ditolak."
        │
        └── Memiliki akses
                │
                ▼
        Sistem menampilkan halaman Edit Barcode
        Seluruh data sebelumnya otomatis terisi
        Barcode ID tidak dapat diubah (read-only)
                │
                ▼
        User mengubah informasi yang diperlukan:
        - Site (dapat diubah)
        - Material (dapat diubah)
        - Type (dapat diubah)
        - Model (dapat diubah)
        - Serial Number (dapat diubah, tetap harus unik)
        - Status (dapat diubah)
        - Description (dapat diubah)
                │
                ▼
        User menekan tombol "Save"
                │
                ▼
        Sistem melakukan validasi (sama seperti Create)
                │
                ├── Validasi gagal
                │       └── Tampilkan error pada field yang bermasalah
                │
                └── Validasi berhasil
                        │
                        ▼
                Sistem membandingkan data lama dan baru
                        │
                        ├── Tidak ada perubahan
                        │       └── Tampilkan: "Tidak ada data yang diubah."
                        │
                        └── Ada perubahan
                                │
                                ▼
                        Sistem menyimpan perubahan ke tabel barcodes
                                │
                                ▼
                        Sistem membuat History:
                        - Jika hanya data berubah → change_type: UPDATE
                        - Jika status berubah → change_type: STATUS_CHANGE
                        - Jika keduanya berubah → buat 2 History terpisah
                                │
                                ▼
                        Sistem mencatat ke Audit Log
                                │
                                ▼
                        Sistem mencatat ke Activity Log: "edit_barcode"
                                │
                                ▼
                        Sistem memperbarui Spreadsheet (baris yang sama)
                                │
                                ▼
                        Barcode Code 128 tetap sama
                                │
                                ▼
                        Tampilkan halaman Detail Barcode dengan data terbaru
```

### 5.2 Aturan Edit Barcode

- Barcode ID **tidak berubah**.
- Barcode Code 128 **tetap sama** (karena dihasilkan dari Barcode ID).
- Barcode fisik tidak perlu dicetak ulang.
- Setiap perubahan wajib dicatat pada Barcode History.
- Seluruh validasi pada Create juga berlaku pada Edit.
- Serial Number tetap harus unik (tidak boleh sama dengan Barcode lain).

### 5.3 Field yang Dapat Diubah

| Field | Dapat Diubah |
|-------|:------------:|
| Site | ✅ |
| Material | ✅ |
| Type | ✅ |
| Model | ✅ |
| Serial Number | ✅ |
| Status | ✅ |
| Description | ✅ |

### 5.4 Field yang Tidak Dapat Diubah

| Field | Dapat Diubah |
|-------|:------------:|
| Barcode ID | ❌ |
| Barcode Code 128 | ❌ (mengikuti Barcode ID) |

---

## 6. Scan Code128

### 6.1 Workflow Scan

```
User berada di halaman Create atau Edit Barcode
        │
        ▼
User menekan tombol "Scan Code 128"
        │
        ▼
Sistem membuka dialog scan (menggunakan kamera device)
        │
        ▼
User mengarahkan kamera ke Barcode Code 128 fisik
        │
        ▼
Sistem membaca nilai Barcode Code 128
        │
        ├── Barcode terbaca
        │       │
        │       ▼
        │   Sistem mencari Barcode ID yang sesuai di database
        │       │
        │       ├── Barcode ID ditemukan
        │       │       │
        │       │       ▼
        │       │   Serial Number dari Barcode tersebut otomatis terisi
        │       │   Tampilkan: "Barcode berhasil dipindai."
        │       │
        │       └── Barcode ID tidak ditemukan
        │               │
        │               ▼
        │           Tampilkan: "Barcode tidak dikenali."
        │           User dapat mengisi Serial Number secara manual
        │
        └── Barcode tidak terbaca / error
                │
                ▼
        Tampilkan: "Gagal memindai Barcode. Silakan coba lagi."
        User dapat mengisi Serial Number secara manual
```

### 6.2 Aturan Scan

- Sistem harus mendukung scan Barcode Code 128 untuk mengisi Serial Number.
- Apabila hasil scan berhasil, Serial Number terisi otomatis.
- Apabila hasil scan gagal, user dapat mengisi Serial Number secara manual.

---

## 7. Search

### 7.1 Pencarian Cepat (Search Box)

Setiap halaman daftar data memiliki kotak pencarian (Search Box).

Pencarian dilakukan secara **real-time** tanpa perlu me-refresh halaman.

### 7.2 Pencarian Berdasarkan

| Kriteria | Keterangan |
|----------|------------|
| Barcode ID | Mencari berdasarkan Barcode ID |
| Serial Number | Mencari berdasarkan Serial Number |
| Site | Mencari berdasarkan Site ID atau Site Name |
| Material | Mencari berdasarkan Material Name |

### 7.3 Aturan Search

- Pencarian menggunakan Server Side Pagination.
- Hasil pencarian langsung diperbarui saat user mengetik.
- User hanya melihat data yang menjadi hak aksesnya.

---

## 8. Filter

### 8.1 Filter Site

User dapat memfilter berdasarkan:
- Site ID
- Site Name

### 8.2 Filter Material

User dapat memfilter berdasarkan:
- Material Name

### 8.3 Filter Type

User dapat memfilter berdasarkan:
- Type

### 8.4 Filter Model

User dapat memfilter berdasarkan:
- Model

### 8.5 Filter Status

User dapat memfilter berdasarkan:
- NEW (MOS)
- OLD (DISMANTLE)

### 8.6 Filter Serial Number

User dapat mencari Barcode menggunakan Serial Number secara langsung.

### 8.7 Filter Barcode ID

User dapat mencari Barcode menggunakan Barcode ID.

### 8.8 Filter Tanggal

User dapat memfilter berdasarkan:
- Tanggal Pembuatan (Date Range)
- Tanggal Perubahan (Date Range)

### 8.9 Kombinasi Filter

Semua filter dapat digunakan secara bersamaan.

Contoh kombinasi:
- Site ID + Material + Type + Model + Status

### 8.10 Tombol Filter

| Tombol | Fungsi |
|--------|--------|
| Search | Menjalankan filter |
| Reset | Mengembalikan seluruh filter ke kondisi awal |

### 8.11 Aturan Filter

- Sistem mendukung penggunaan lebih dari satu filter secara bersamaan.
- Sistem mengingat filter terakhir yang digunakan selama sesi login masih aktif.
- Apabila tidak ada data yang sesuai: "Tidak ada data yang sesuai dengan filter yang dipilih."
- Filter hanya menampilkan data yang berhak diakses oleh user.

---

## 9. Detail Barcode

### 9.1 Informasi Lengkap

Halaman Detail Barcode menampilkan:

**Informasi Barcode:**
- Barcode ID
- Status
- Tanggal Dibuat
- Terakhir Diubah
- Dibuat Oleh
- Terakhir Diubah Oleh

**Barcode Code 128:**
- Tampilan Barcode Code 128 ukuran besar
- Nilai Barcode ID di bawah barcode
- Tombol: Print Barcode, Download Barcode

**Informasi Material:**
- Site ID
- Site Name
- Material Name
- Type
- Model
- Serial Number
- Status

Seluruh field informasi material bersifat **Read Only**.

### 9.2 History / Timeline

Riwayat perubahan ditampilkan dalam bentuk tabel:

| Kolom | Keterangan |
|-------|------------|
| Tanggal | Waktu perubahan terjadi |
| User | User yang melakukan perubahan |
| Jenis Perubahan | CREATE, UPDATE, STATUS_CHANGE, RESTORE, SOFT_DELETE |
| Ringkasan Perubahan | Deskripsi singkat perubahan |

- Tombol **Lihat Detail** untuk melihat perubahan secara lengkap (old_values, new_values).
- Urutan dari perubahan terbaru ke paling lama.
- Gunakan Pagination apabila data lebih dari 10 per halaman.
- Apabila belum ada riwayat: "Belum ada riwayat perubahan."

### 9.3 Action Button

| Tombol | Fungsi |
|--------|--------|
| Edit Barcode | Membuka halaman Edit Barcode |
| Print Barcode | Mencetak Barcode |
| Kembali | Kembali ke halaman sebelumnya |

Hak akses mengikuti Role pengguna.

### 9.4 Aturan Detail

- Data Barcode dan History dimuat secara terpisah agar halaman tetap cepat.
- History bersifat immutable — tidak dapat diedit atau dihapus.

---

## 10. Spreadsheet

### 10.1 Pencatatan Otomatis

Setiap Barcode yang berhasil dibuat **wajib otomatis tercatat** pada Spreadsheet.

Pencatatan dilakukan **setelah proses Save berhasil**.

Apabila proses Save gagal, Spreadsheet **tidak boleh diperbarui**.

### 10.2 Data yang Dicatat

Spreadsheet minimal berisi:

| Kolom | Keterangan |
|-------|------------|
| Nomor Urut | Auto-increment |
| Barcode ID | ID unik Barcode |
| Site ID | ID Site |
| Site Name | Nama Site |
| Material Name | Nama Material |
| Type | Tipe Material |
| Model | Model Material |
| Serial Number | Nomor Seri |
| Status | NEW / OLD |
| Dibuat Oleh | User yang membuat |
| Tanggal Dibuat | Waktu pembuatan |
| Terakhir Diubah | Waktu terakhir diubah |
| Link Barcode | Link ke halaman Detail Barcode |

### 10.3 Update Spreadsheet

Apabila informasi Barcode berubah, **baris yang sama** pada Spreadsheet diperbarui.

Sistem **tidak membuat baris baru**.

### 10.4 Soft Delete

Apabila Barcode di-soft delete, status pada Spreadsheet berubah menjadi **"Inactive"**.

Data tidak boleh dihapus dari Spreadsheet.

### 10.5 Restore

Apabila Barcode di-restore, Spreadsheet diperbarui kembali menjadi aktif.

### 10.6 Kegagalan Sinkronisasi

Apabila terjadi kegagalan sinkronisasi:
- Sistem mencatat kegagalan pada Audit Log.
- Sistem menyediakan mekanisme sinkronisasi ulang.

### 10.7 Hak Akses Spreadsheet

- User hanya dapat melihat data sesuai hak aksesnya.
- Admin dapat melihat seluruh data Spreadsheet.

---

## 11. Audit Log

### 11.1 Kapan Audit Log Dibuat

Audit Log dibuat secara otomatis oleh sistem pada saat:

| Aktivitas | Keterangan |
|-----------|------------|
| Membuat Barcode | Setelah Barcode berhasil disimpan |
| Mengubah Barcode | Setelah perubahan Barcode berhasil disimpan |
| Mengubah Status | Saat status Barcode berubah |
| Menghapus Barcode (Soft Delete) | Saat Barcode di-soft delete |
| Restore Barcode | Saat Barcode di-restore |
| Login | Setiap kali user login |
| Logout | Setiap kali user logout |
| Gagal Login | Setiap percobaan login gagal |
| Pembuatan Akun | Admin membuat akun baru |
| Verifikasi Akun | Admin memverifikasi akun |
| Aktivasi Akun | Admin mengaktifkan akun |
| Reset Password | Admin mereset password user |
| Perubahan Password | User mengubah password sendiri |
| Perubahan Role | Super Admin mengubah Role user |
| Penonaktifan Akun | Admin menonaktifkan akun |
| Perubahan Master Site | Admin menambah/mengubah/menonaktifkan Site |
| Perubahan Master Material | Admin menambah/mengubah/menonaktifkan Material |
| Kegagalan Sinkronisasi Spreadsheet | Saat sinkronisasi gagal |

### 11.2 Data yang Dicatat

Setiap Audit Log mencatat:
- User ID — siapa yang melakukan
- Entity Type — jenis data (barcode, user, site, material, dll)
- Entity ID — ID data yang berubah
- Action — jenis aksi (create, update, delete, restore, login, logout, export, import)
- Old Values — data sebelum perubahan (JSON)
- New Values — data setelah perubahan (JSON)
- IP Address — alamat IP user
- User Agent — browser / device
- Created At — waktu kejadian

### 11.3 Aturan Audit Log

- Audit Log bersifat **immutable** — tidak dapat diubah atau dihapus.
- Tidak menggunakan softDeletes().
- Tidak menggunakan updated_at — hanya created_at.

---

## 12. Activity Log

### 12.1 Kapan Activity Log Dibuat

Activity Log dibuat secara otomatis oleh sistem pada saat:

| Aktivitas | Modul |
|-----------|-------|
| login | Authentication |
| logout | Authentication |
| generate_barcode | Barcode |
| edit_barcode | Barcode |
| delete_barcode | Barcode |
| export_spreadsheet | Spreadsheet |
| import_master_site | Site |
| import_master_material | Material |
| scan_barcode | Barcode |
| search_barcode | Barcode |

### 12.2 Data yang Dicatat

Setiap Activity Log mencatat:
- User ID — user yang melakukan aktivitas
- Activity — nama aktivitas
- Module — nama modul
- Description — penjelasan singkat (contoh: "Barcode BR000012 berhasil dibuat.")
- IP Address — alamat IP user
- User Agent — browser / device
- Session ID — session Laravel
- Created At — waktu aktivitas

### 12.3 Aturan Activity Log

- Activity Log bersifat **immutable** — tidak dapat diubah atau dihapus.
- Tidak menggunakan softDeletes().
- Tidak menggunakan updated_at — hanya created_at.

---

## 13. Error Handling

### 13.1 Data Tidak Ditemukan

| Skenario | Pesan |
|----------|-------|
| Barcode tidak ditemukan | "Barcode tidak ditemukan." |
| Material tidak ditemukan | "Material tidak ditemukan." |
| Site tidak ditemukan | "Site tidak ditemukan." |
| User tidak ditemukan | "User tidak ditemukan." |
| History tidak ditemukan | "Belum ada riwayat perubahan." |
| Hasil filter kosong | "Tidak ada data yang sesuai dengan filter yang dipilih." |

### 13.2 SN Duplikat

Apabila Serial Number sudah digunakan oleh Barcode lain:
```
"Serial Number sudah digunakan. Silakan gunakan Serial Number lain."
```
Proses penyimpanan dibatalkan.

### 13.3 Site Tidak Ada

Apabila Site tidak tersedia di Master Site:
```
User memilih: "None / Site Not Detected"
```

### 13.4 Material Tidak Ada

Apabila Material belum tersedia di Master Material:
```
User tidak dapat membuat Material sendiri.
User harus mengajukan kepada Admin.
Admin dapat menambahkan Material baru melalui menu Master Material.
```

### 13.5 Akses Ditolak

| Skenario | Pesan |
|----------|-------|
| User tanpa hak akses menekan Generate | "Akses ditolak." |
| User tanpa hak akses menekan Edit | "Akses ditolak." |
| User tanpa hak akses membuka halaman Admin | Redirect ke Dashboard dengan pesan: "Anda tidak memiliki akses ke halaman ini." |

### 13.6 Validasi Gagal

Apabila terdapat satu saja data yang tidak valid:
- Proses penyimpanan dibatalkan.
- Field yang bermasalah diberi tanda merah.
- Tampilkan pesan error yang jelas.

### 13.7 Scan Gagal

Apabila hasil scan tidak valid atau tidak dapat dibaca:
```
"Gagal memindai Barcode. Silakan coba lagi."
```
User dapat mengisi Serial Number secara manual.

### 13.8 Sinkronisasi Spreadsheet Gagal

Apabila terjadi kegagalan sinkronisasi Spreadsheet:
- Catat kegagalan pada Audit Log.
- Tampilkan notifikasi pada Dashboard (khusus Admin).
- Sediakan mekanisme sinkronisasi ulang.

---

## 14. Hak Akses

### 14.1 Super Admin

Hak penuh terhadap seluruh sistem.

| Fitur | Akses |
|-------|:-----:|
| Login / Logout | ✅ |
| Dashboard (seluruh data) | ✅ |
| Generate Barcode | ✅ |
| Edit Barcode | ✅ |
| Delete / Restore Barcode | ✅ |
| Lihat Detail Barcode | ✅ |
| Scan Barcode | ✅ |
| Filter & Search | ✅ |
| Spreadsheet (seluruh data) | ✅ |
| Audit Log | ✅ |
| Activity Log | ✅ |
| Master Site (CRUD) | ✅ |
| Master Material (CRUD) | ✅ |
| Master Type (CRUD) | ✅ |
| Master Model (CRUD) | ✅ |
| User Management (penuh) | ✅ |
| Ubah Role | ✅ |
| Ubah Permission | ✅ |

### 14.2 Admin

| Fitur | Akses |
|-------|:-----:|
| Login / Logout | ✅ |
| Dashboard (seluruh data) | ✅ |
| Generate Barcode | ✅ |
| Edit Barcode | ✅ |
| Delete / Restore Barcode | ✅ |
| Lihat Detail Barcode | ✅ |
| Scan Barcode | ✅ |
| Filter & Search | ✅ |
| Spreadsheet (seluruh data) | ✅ |
| Audit Log | ✅ |
| Activity Log | ❌ |
| Master Site (CRUD) | ✅ |
| Master Material (CRUD) | ✅ |
| Master Type (CRUD) | ✅ |
| Master Model (CRUD) | ✅ |
| User Management (CRUD) | ✅ (tidak dapat mengubah Super Admin) |
| Ubah Role | ❌ (hanya Super Admin) |
| Ubah Permission | ❌ (hanya Super Admin) |

### 14.3 User

| Fitur | Akses |
|-------|:-----:|
| Login / Logout | ✅ |
| Dashboard (data sendiri) | ✅ |
| Generate Barcode | ✅ |
| Edit Barcode (yang diizinkan) | ✅ |
| Delete / Restore Barcode | ❌ |
| Lihat Detail Barcode (yang diizinkan) | ✅ |
| Scan Barcode | ✅ |
| Filter & Search (data sendiri) | ✅ |
| Spreadsheet (data sendiri) | ✅ |
| Audit Log | ❌ |
| Activity Log | ❌ |
| Master Site | ❌ |
| Master Material | ❌ |
| Master Type | ❌ |
| Master Model | ❌ |
| User Management | ❌ |
| Ubah Password Sendiri | ✅ |

### 14.4 Aturan Hak Akses

- Setiap halaman, API, dan aksi harus memeriksa Permission sebelum dijalankan.
- Menu yang ditampilkan mengikuti Role dan Permission yang dimiliki.
- User tidak boleh melihat menu yang tidak memiliki hak akses.

---

## 15. Workflow Diagram

### 15.1 Diagram Utama Aplikasi

```
                        ┌─────────────┐
                        │   LOGIN     │
                        └──────┬──────┘
                               │
                               ▼
                        ┌─────────────┐
                        │  DASHBOARD  │
                        └──────┬──────┘
                               │
              ┌────────────────┼──────────────────┐
              │                │                   │
              ▼                ▼                   ▼
    ┌─────────────────┐ ┌─────────────┐ ┌─────────────────┐
    │ GENERATE BARCODE│ │ SCAN BARCODE│ │  BARCODE LIST   │
    └────────┬────────┘ └──────┬──────┘ └────────┬────────┘
             │                 │                  │
             ▼                 ▼                  ├──────────────┐
    ┌─────────────────┐ ┌─────────────┐          │              │
    │  FORM BARCODE   │ │ ISI SN      │          ▼              ▼
    │  (Isi Data)     │ │ OTOMATIS    │  ┌─────────────┐ ┌─────────────┐
    └────────┬────────┘ └─────────────┘  │ FILTER/SEARCH│ │  DETAIL     │
             │                           └─────────────┘ │  BARCODE    │
             ▼                                           └──────┬──────┘
    ┌─────────────────┐                                          │
    │  VALIDASI       │                                          ├──────────────┐
    └────────┬────────┘                                          │              │
             │                                                   ▼              ▼
    ┌────────┴────────┐                                  ┌─────────────┐ ┌─────────────┐
    │  VALID?         │                                  │  HISTORY    │ │  EDIT       │
    └────────┬────────┘                                  │  TIMELINE   │ │  BARCODE    │
             │                                           └─────────────┘ └──────┬──────┘
    ┌────────┴────────┐                                                         │
    │  YES      NO    │                                                         ▼
    └────────┬────────┘                                                  ┌─────────────┐
             │                                                           │  FORM EDIT  │
             ▼                                                           │  (Data Lama)│
    ┌─────────────────┐                                                  └──────┬──────┘
    │  SAVE BARCODE   │                                                         │
    └────────┬────────┘                                                         ▼
             │                                                           ┌─────────────┐
             ▼                                                           │  VALIDASI   │
    ┌─────────────────┐                                                  └──────┬──────┘
    │  GENERATE       │                                                         │
    │  CODE 128       │                                                  ┌──────┴──────┐
    └────────┬────────┘                                                  │  YES    NO  │
             │                                                           └──────┬──────┘
             ▼                                                                  │
    ┌─────────────────┐                                                          │
    │  CATAT KE:      │                                                          │
    │  - History      │                                                          │
    │  - Audit Log    │                                                          │
    │  - Activity Log │                                                          │
    │  - Spreadsheet  │                                                          │
    └────────┬────────┘                                                          │
             │                                                                   │
             ▼                                                                   │
    ┌─────────────────┐                                                          │
    │  DETAIL BARCODE │◄─────────────────────────────────────────────────────────┘
    │  + CODE 128     │
    └────────┬────────┘
             │
             ▼
    ┌─────────────────┐
    │  PRINT / DOWNLOAD│
    └─────────────────┘
```

### 15.2 Diagram Soft Delete & Restore

```
                    ┌─────────────────┐
                    │  DETAIL BARCODE │
                    └────────┬────────┘
                             │
              ┌──────────────┴──────────────┐
              │                             │
              ▼                             ▼
    ┌─────────────────┐           ┌─────────────────┐
    │  SOFT DELETE    │           │     RESTORE     │
    └────────┬────────┘           └────────┬────────┘
             │                             │
             ▼                             ▼
    ┌─────────────────┐           ┌─────────────────┐
    │  deleted_at =   │           │  deleted_at =   │
    │  timestamp      │           │  null           │
    └────────┬────────┘           └────────┬────────┘
             │                             │
             ▼                             ▼
    ┌─────────────────┐           ┌─────────────────┐
    │  History:       │           │  History:       │
    │  SOFT_DELETE    │           │  RESTORE        │
    └────────┬────────┘           └────────┬────────┘
             │                             │
             ▼                             ▼
    ┌─────────────────┐           ┌─────────────────┐
    │  Audit Log      │           │  Audit Log      │
    └────────┬────────┘           └────────┬────────┘
             │                             │
             ▼                             ▼
    ┌─────────────────┐           ┌─────────────────┐
    │  Spreadsheet:   │           │  Spreadsheet:   │
    │  "Inactive"     │           │  "Active"       │
    └─────────────────┘           └─────────────────┘
```


