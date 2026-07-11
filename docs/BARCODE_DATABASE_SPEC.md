# BARCODE DATABASE SPECIFICATION

## Tujuan

Dokumen ini mendefinisikan secara detail spesifikasi database untuk domain Barcode pada Barcode Management System.

Dokumen ini menjadi acuan utama dalam pembuatan migration, model, repository, service, dan API yang berkaitan dengan Barcode.

---

## 1. Fungsi Tabel barcodes

Tabel `barcodes` berfungsi sebagai tabel utama yang menyimpan seluruh informasi Barcode yang telah di-generate oleh sistem.

Setiap baris pada tabel ini merepresentasikan satu Barcode unik yang terkait dengan satu Material, satu Site, dan memiliki satu Serial Number.

Tabel ini menjadi pusat dari seluruh fitur Barcode, termasuk pencarian, filtering, export, dan pelacakan.

---

## 2. Fungsi Tabel barcode_histories

Tabel `barcode_histories` berfungsi sebagai tabel riwayat (history) yang mencatat setiap perubahan yang terjadi pada data Barcode.

Setiap kali data Barcode diubah (Create, Update, Status Change, Soft Delete, Restore), sistem secara otomatis menambahkan satu baris baru ke tabel ini.

Tabel ini bersifat **immutable** — data yang sudah tercatat tidak boleh diedit atau dihapus oleh User maupun Admin.

---

## 3. Relasi

### 3.1 Relasi dengan materials

```
barcodes.material_id → materials.id
```

- Satu Barcode memiliki satu Material.
- Satu Material dapat digunakan oleh banyak Barcode.
- Foreign Key menggunakan `cascadeOnDelete()` — apabila Material dihapus, seluruh Barcode yang terkait ikut terhapus (soft delete).
- Relasi ini memungkinkan informasi Material (nama, type, model) selalu sinkron dengan Barcode.

### 3.2 Relasi dengan sites

```
barcodes.site_id → sites.id
```

- Satu Barcode memiliki satu Site.
- Satu Site dapat digunakan oleh banyak Barcode.
- Foreign Key menggunakan `cascadeOnDelete()` — apabila Site dihapus, seluruh Barcode yang terkait ikut terhapus (soft delete).
- Relasi ini memungkinkan pelacakan lokasi material berdasarkan Site.

### 3.3 Relasi dengan users

```
barcodes.created_by → users.id
barcodes.updated_by → users.id (nullable)
```

- `created_by` mencatat User yang membuat Barcode.
- `updated_by` mencatat User yang terakhir mengubah Barcode.
- Foreign Key menggunakan `nullOnDelete()` — apabila User dihapus, data Barcode tetap ada namun referensi User menjadi null.

### 3.4 Relasi barcode_histories dengan barcodes

```
barcode_histories.barcode_id → barcodes.id
```

- Satu History selalu mengacu pada satu Barcode.
- Satu Barcode dapat memiliki banyak History.
- Foreign Key menggunakan `cascadeOnDelete()` — apabila Barcode dihapus (soft delete), seluruh History tetap ada untuk keperluan audit.

### 3.5 Relasi barcode_histories dengan users

```
barcode_histories.user_id → users.id
```

- Mencatat User yang melakukan perubahan.
- Foreign Key menggunakan `nullOnDelete()` — apabila User dihapus, data History tetap ada.

---

## 4. Struktur Kolom Tabel barcodes

### 4.1 id

- Tipe: `bigIncrements` (auto-increment)
- Primary Key tabel.
- Digunakan sebagai referensi utama untuk relasi dengan tabel lain.

### 4.2 barcode_id

- Tipe: `string(50)`
- **Unique Index**
- **Permanen** — tidak dapat diubah setelah dibuat.
- Format: `BRC-XXXXXXXXXX` (contoh: `BRC-20260709-001`).
- Digunakan sebagai identitas visual Barcode yang ditampilkan ke user dan dicetak sebagai Barcode Code 128.
- Tidak menggunakan auto-increment karena bersifat permanen dan tidak boleh berubah.

### 4.3 material_id

- Tipe: `bigInteger` (unsigned)
- Foreign Key ke `materials.id`.
- Menghubungkan Barcode dengan Material tertentu.
- Wajib diisi.

### 4.4 site_id

- Tipe: `bigInteger` (unsigned)
- Foreign Key ke `sites.id`.
- Menentukan lokasi Site tempat material berada.
- Wajib diisi.

### 4.5 serial_number

- Tipe: `string(255)`
- **Unique Index**
- Wajib diisi.
- Dapat diisi secara manual atau melalui scan Barcode Code 128.
- Tidak boleh ada duplikasi Serial Number di seluruh sistem.

### 4.6 status

- Tipe: `string(20)` atau `enum`
- Nilai yang valid:
  - `NEW` / `MOS` — Material baru / Masih Operasional
  - `OLD` / `DISMANTLE` — Material lama / Dibongkar
- Default: `NEW`
- Status dapat diubah sesuai kondisi material di lapangan.

### 4.7 description

- Tipe: `text`
- Nullable.
- Catatan tambahan mengenai Barcode atau material.

### 4.8 is_active

- Tipe: `boolean`
- Default: `true`
- Digunakan untuk Soft Delete secara manual.
- Apabila `false`, Barcode dianggap tidak aktif.

### 4.9 created_by

- Tipe: `bigInteger` (unsigned)
- Foreign Key ke `users.id`.
- Nullable (menggunakan `nullOnDelete()`).
- Mencatat User yang membuat Barcode.

### 4.10 updated_by

- Tipe: `bigInteger` (unsigned)
- Foreign Key ke `users.id`.
- Nullable.
- Mencatat User yang terakhir mengubah Barcode.

### 4.11 created_at

- Tipe: `timestamp`
- Nullable.
- Diisi otomatis oleh Laravel.

### 4.12 updated_at

- Tipe: `timestamp`
- Nullable.
- Diisi otomatis oleh Laravel.

### 4.13 deleted_at

- Tipe: `timestamp`
- Nullable.
- Digunakan untuk Soft Delete.
- Barcode tidak boleh dihapus secara permanen.

---

## 5. Struktur Kolom Tabel barcode_histories

### 5.1 id

- Tipe: `bigIncrements` (auto-increment)
- Primary Key tabel.

### 5.2 barcode_id

- Tipe: `bigInteger` (unsigned)
- Foreign Key ke `barcodes.id`.
- Menghubungkan History dengan Barcode tertentu.
- Wajib diisi.

### 5.3 user_id

- Tipe: `bigInteger` (unsigned)
- Foreign Key ke `users.id`.
- Nullable (menggunakan `nullOnDelete()`).
- Mencatat User yang melakukan perubahan.

### 5.4 change_type

- Tipe: `string(50)` atau `enum`
- Jenis perubahan yang terjadi.
- Nilai yang valid:
  - `CREATE` — Barcode baru dibuat
  - `UPDATE` — Data Barcode diubah
  - `STATUS_CHANGE` — Status Barcode berubah
  - `RESTORE` — Barcode di-restore
  - `SOFT_DELETE` — Barcode di-soft delete

### 5.5 old_data

- Tipe: `json` atau `text` (JSON)
- Nullable.
- Menyimpan data Barcode sebelum perubahan dalam format JSON.
- Memungkinkan rollback atau audit secara detail.

### 5.6 new_data

- Tipe: `json` atau `text` (JSON)
- Nullable.
- Menyimpan data Barcode setelah perubahan dalam format JSON.

### 5.7 summary

- Tipe: `string(255)`
- Nullable.
- Ringkasan perubahan yang ditampilkan pada halaman Detail Barcode.
- Contoh: "Mengubah Status dari NEW ke OLD", "Mengubah Site dari Site-A ke Site-B".

### 5.8 created_at

- Tipe: `timestamp`
- Nullable.
- Diisi otomatis oleh Laravel.
- Mencatat waktu perubahan terjadi.

### 5.9 Catatan

- Tabel `barcode_histories` **tidak memiliki** `updated_at` dan `deleted_at` karena data bersifat immutable (tidak dapat diubah atau dihapus).

---

## 6. Aturan Barcode ID

### 6.1 Permanen

Barcode ID bersifat permanen dan tidak berubah sepanjang siklus hidup Barcode.

### 6.2 Tidak Berubah

Apabila informasi material berubah (Site, Material Name, Type, Model, Serial Number, Status), Barcode ID tetap sama.

### 6.3 Unique

Tidak boleh ada dua Barcode dengan Barcode ID yang sama.

### 6.4 Format

Barcode ID menggunakan format yang mudah dibaca manusia, misalnya:
- `BRC-YYYYMMDD-XXX`
- Atau format lain yang ditentukan oleh sistem.

### 6.5 Barcode Code 128

Barcode Code 128 dihasilkan berdasarkan Barcode ID.
Karena Barcode ID tidak berubah, Barcode Code 128 juga tidak berubah.
Barcode tidak perlu dicetak ulang apabila informasi material berubah.

---

## 7. Aturan Serial Number

### 7.1 Wajib Diisi

Serial Number tidak boleh kosong.

### 7.2 Unique

Tidak boleh ada dua Barcode dengan Serial Number yang sama.

### 7.3 Scan Code 128

Sistem mendukung scan Barcode Code 128 untuk mengisi Serial Number secara otomatis.

### 7.4 Input Manual

User juga dapat mengisi Serial Number secara manual.

---

## 8. Aturan Status Barcode

### 8.1 Dua Status Utama

| Status | Keterangan |
|--------|------------|
| NEW / MOS | Material baru atau masih dalam kondisi operasional |
| OLD / DISMANTLE | Material lama atau sudah dibongkar |

### 8.2 Default

Saat Barcode pertama kali dibuat, status default adalah `NEW`.

### 8.3 Dapat Diubah

Status dapat diubah sesuai kondisi material di lapangan.
Perubahan status dicatat pada Barcode History.

---

## 9. Aturan Edit Barcode

### 9.1 Tanpa Membuat Barcode Baru

User dapat mengubah informasi Barcode tanpa membuat Barcode baru.

### 9.2 Barcode ID Tetap

Barcode ID tidak berubah saat edit.

### 9.3 Barcode Code 128 Tetap

Karena Barcode Code 128 dihasilkan dari Barcode ID, maka Barcode Code 128 juga tidak berubah.

### 9.4 Field yang Dapat Diubah

- Site
- Material
- Type
- Model
- Serial Number
- Status
- Description

### 9.5 Field yang Tidak Dapat Diubah

- Barcode ID

### 9.6 Riwayat Perubahan

Setiap edit wajib dicatat pada Barcode History.

---

## 10. Alur Pencatatan Otomatis ke Spreadsheet

### 10.1 Trigger

Pencatatan ke Spreadsheet dilakukan setelah proses Save Barcode berhasil.

### 10.2 Data yang Dicatat

Spreadsheet mencatat:
- Nomor Urut
- Barcode ID
- Site ID
- Site Name
- Material Name
- Type
- Model
- Serial Number
- Status
- Dibuat Oleh
- Tanggal Dibuat
- Terakhir Diubah
- Link Barcode

### 10.3 Update Spreadsheet

Apabila informasi Barcode berubah, baris yang sama pada Spreadsheet diperbarui.
Sistem tidak membuat baris baru.

### 10.4 Soft Delete

Apabila Barcode di-soft delete, status pada Spreadsheet berubah menjadi "Inactive".

### 10.5 Restore

Apabila Barcode di-restore, Spreadsheet diperbarui kembali menjadi aktif.

### 10.6 Kegagalan Sinkronisasi

Apabila terjadi kegagalan sinkronisasi, sistem mencatat kegagalan pada Audit Log dan menyediakan mekanisme sinkronisasi ulang.

---

## 11. Riwayat Perubahan Data

### 11.1 Pencatatan Otomatis

Setiap perubahan data Barcode otomatis membuat History baru.

### 11.2 Data yang Dicatat

Setiap History mencatat:
- Barcode ID
- Tanggal & Waktu Perubahan
- User yang melakukan perubahan
- Data sebelum perubahan (old_data)
- Data setelah perubahan (new_data)
- Jenis perubahan (change_type)
- Ringkasan perubahan (summary)

### 11.3 Immutable

History bersifat permanen dan tidak dapat diedit atau dihapus.

### 11.4 Urutan

History ditampilkan secara kronologis dari yang terbaru ke yang paling lama.

### 11.5 Filter

History dapat difilter berdasarkan:
- Barcode ID
- Serial Number
- User
- Jenis Perubahan
- Rentang Tanggal

---

## 12. Strategi Soft Delete

### 12.1 Tabel barcodes

- Menggunakan `softDeletes()`.
- Barcode tidak boleh dihapus secara permanen.
- Gunakan Soft Delete apabila Barcode tidak lagi digunakan.
- Data yang di-soft delete masih dapat di-restore.

### 12.2 Tabel barcode_histories

- **Tidak menggunakan** Soft Delete.
- History bersifat immutable dan tidak boleh dihapus dalam kondisi apapun.

### 12.3 Dampak Soft Delete

- Barcode yang di-soft delete tidak muncul pada pencarian default.
- Spreadsheet menampilkan status "Inactive".
- History tetap tersedia untuk keperluan audit.

---

## 13. Strategi Foreign Key

### 13.1 Daftar Foreign Key

| Tabel | Kolom | Referensi | On Delete |
|-------|-------|-----------|-----------|
| barcodes | material_id | materials.id | cascadeOnDelete |
| barcodes | site_id | sites.id | cascadeOnDelete |
| barcodes | created_by | users.id | nullOnDelete |
| barcodes | updated_by | users.id | nullOnDelete |
| barcode_histories | barcode_id | barcodes.id | cascadeOnDelete |
| barcode_histories | user_id | users.id | nullOnDelete |

### 13.2 Alasan cascadeOnDelete

- `material_id` dan `site_id` menggunakan `cascadeOnDelete` karena Barcode tidak valid tanpa Material atau Site yang sesuai.
- `barcode_id` pada history menggunakan `cascadeOnDelete` karena History tidak bermakna tanpa Barcode yang dirujuk.

### 13.3 Alasan nullOnDelete

- `created_by` dan `updated_by` menggunakan `nullOnDelete` karena data Barcode dan History harus tetap ada meskipun User dihapus.

---

## 14. Strategi Indexing untuk Performa Pencarian

### 14.1 Primary Key

- `barcodes.id` — Primary Key
- `barcode_histories.id` — Primary Key

### 14.2 Unique Index

- `barcodes.barcode_id` — Unique Index (pencarian berdasarkan Barcode ID)
- `barcodes.serial_number` — Unique Index (pencarian berdasarkan Serial Number)

### 14.3 Foreign Key Index

- `barcodes.material_id` — Index (join dengan materials)
- `barcodes.site_id` — Index (join dengan sites)
- `barcodes.created_by` — Index (join dengan users)
- `barcodes.updated_by` — Index (join dengan users)
- `barcode_histories.barcode_id` — Index (join dengan barcodes)
- `barcode_histories.user_id` — Index (join dengan users)

### 14.4 Composite Index untuk Pencarian

- `barcodes.status` + `barcodes.is_active` — Index untuk filter status dan aktif/non-aktif
- `barcode_histories.barcode_id` + `barcode_histories.created_at` — Index untuk menampilkan history kronologis per Barcode

### 14.5 Catatan

Laravel secara otomatis membuat index untuk kolom Foreign Key yang didefinisikan dengan `foreignId()->constrained()`.
Index tambahan dapat ditambahkan pada kolom yang sering digunakan untuk filter dan pencarian.

---

## 15. Rekomendasi Penggunaan Enum

### 15.1 Status Barcode

Direkomendasikan menggunakan `string` dengan validasi di level Laravel (Rule/Validation) daripada database native enum.

Alasan:
- PostgreSQL memiliki dukungan enum, tetapi migrasi lebih kompleks.
- Menggunakan string lebih fleksibel untuk perubahan di masa depan.
- Validasi tetap ketat di level aplikasi.

Nilai yang valid:
- `NEW`
- `OLD`

### 15.2 Change Type (Barcode History)

Direkomendasikan menggunakan `string` dengan validasi di level Laravel.

Nilai yang valid:
- `CREATE`
- `UPDATE`
- `STATUS_CHANGE`
- `RESTORE`
- `SOFT_DELETE`

---

## 16. Workflow Generate Barcode hingga Edit Barcode

### 16.1 Generate Barcode

```
1. User membuka halaman Create Barcode
2. User memilih Site (Dropdown + Search)
   - Site Name otomatis mengikuti Site ID
   - Apabila tidak tersedia: None / Site Not Detected
3. User memilih Material (Dropdown + Search)
   - Type dan Model otomatis mengikuti Material
   - Type dan Model masih dapat diubah
4. User mengisi Serial Number
   - Manual atau Scan Code 128
5. User memilih Status (default: NEW)
6. User menekan tombol Save
7. Sistem melakukan validasi:
   - Site wajib
   - Material wajib
   - Serial Number wajib
   - Status wajib
   - Serial Number unik
8. Sistem menyimpan data ke tabel barcodes
9. Sistem membuat History (change_type: CREATE)
10. Sistem mencatat ke Audit Log
11. Sistem mencatat ke Spreadsheet
12. Sistem menampilkan Barcode Code 128
13. User dapat mencetak atau mengunduh Barcode
```

### 16.2 Edit Barcode

```
1. User membuka halaman Edit Barcode
2. Seluruh data sebelumnya otomatis terisi
3. User mengubah informasi yang diperlukan
   - Barcode ID tidak dapat diubah
   - Site, Material, Type, Model, Serial Number, Status dapat diubah
4. User menekan tombol Save
5. Sistem melakukan validasi
6. Sistem menyimpan perubahan ke tabel barcodes
7. Sistem membuat History (change_type: UPDATE atau STATUS_CHANGE)
8. Sistem mencatat ke Audit Log
9. Sistem memperbarui Spreadsheet (baris yang sama)
10. Barcode Code 128 tetap sama
```

### 16.3 Soft Delete Barcode

```
1. User (dengan hak akses) menekan tombol Delete
2. Sistem mengubah deleted_at menjadi timestamp
3. Sistem membuat History (change_type: SOFT_DELETE)
4. Sistem mencatat ke Audit Log
5. Spreadsheet memperbarui status menjadi "Inactive"
```

### 16.4 Restore Barcode

```
1. User (dengan hak akses) menekan tombol Restore
2. Sistem mengubah deleted_at menjadi null
3. Sistem membuat History (change_type: RESTORE)
4. Sistem mencatat ke Audit Log
5. Spreadsheet memperbarui status kembali menjadi aktif
```

---

## 17. Ringkasan Tabel

### Tabel: barcodes

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| id | bigIncrements | Primary Key | Auto-increment |
| barcode_id | string(50) | Unique Index | Permanen, tidak berubah |
| material_id | bigInteger | FK → materials.id (cascadeOnDelete) | Wajib |
| site_id | bigInteger | FK → sites.id (cascadeOnDelete) | Wajib |
| serial_number | string(255) | Unique Index | Wajib, unik |
| status | string(20) | Default: 'NEW' | NEW / OLD |
| description | text | Nullable | Catatan tambahan |
| is_active | boolean | Default: true | Soft Delete manual |
| created_by | bigInteger | FK → users.id (nullOnDelete) | Nullable |
| updated_by | bigInteger | FK → users.id (nullOnDelete) | Nullable |
| created_at | timestamp | Nullable | Otomatis |
| updated_at | timestamp | Nullable | Otomatis |
| deleted_at | timestamp | Nullable | Soft Delete |

### Tabel: barcode_histories

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|------------|------------|
| id | bigIncrements | Primary Key | Auto-increment |
| barcode_id | bigInteger | FK → barcodes.id (cascadeOnDelete) | Wajib |
| user_id | bigInteger | FK → users.id (nullOnDelete) | Nullable |
| change_type | string(50) | - | CREATE, UPDATE, STATUS_CHANGE, RESTORE, SOFT_DELETE |
| old_data | json | Nullable | Data sebelum perubahan |
| new_data | json | Nullable | Data setelah perubahan |
| summary | string(255) | Nullable | Ringkasan perubahan |
| created_at | timestamp | Nullable | Otomatis (waktu perubahan) |

---

## 18. Catatan Implementasi

- Migration `barcodes` harus dibuat setelah migration `materials`, `sites`, dan `users` selesai.
- Migration `barcode_histories` harus dibuat setelah migration `barcodes` selesai.
- Gunakan `foreignId()->constrained()->cascadeOnDelete()` untuk Foreign Key.
- Gunakan `foreignId()->constrained()->nullOnDelete()` untuk Foreign Key ke users.
- Gunakan `softDeletes()` hanya pada tabel `barcodes`.
- Tabel `barcode_histories` tidak memiliki `updated_at` dan `deleted_at`.
- Index tambahan dapat ditambahkan setelah migration awal sesuai kebutuhan performa.
