# PHASE 2 - TASK 2.13

# Judul

Create Audit Logs Migration

---

# Tujuan

Membuat migration tabel `audit_logs`.

Tabel ini digunakan untuk menyimpan seluruh perubahan data penting yang terjadi pada sistem.

Audit Log digunakan untuk kebutuhan:

- Audit perubahan data
- Riwayat perubahan
- Investigasi
- Keamanan
- Tracking aktivitas perubahan data

Task ini hanya membuat migration tabel `audit_logs`.

---

# Dokumen Referensi

WAJIB membaca:

- docs/PROJECT_STATUS.md
- docs/AI_CONTEXT.md
- docs/DATABASE_FINAL.md
- docs/DATABASE_DESIGN.md
- docs/ERD.md
- docs/MIGRATION_PLAN.md
- docs/BUSINESS_RULES.md
- docs/BARCODE_RULES.md
- docs/HISTORY_RULES.md

---

# Yang Boleh Dikerjakan

- Membuat migration audit_logs.
- Menggunakan Laravel 12.
- Menggunakan JSON untuk penyimpanan perubahan data.
- Menggunakan Foreign Key sesuai spesifikasi.
- Menambahkan index yang diperlukan.

---

# Struktur Minimal

Kolom minimal:

- id
- user_id
- entity_type
- entity_id
- action
- old_values (JSON)
- new_values (JSON)
- ip_address
- user_agent
- created_at

---

# Penjelasan Kolom

## user_id

User yang melakukan perubahan.

Foreign Key ke users.

Nullable.

Menggunakan nullOnDelete().

---

## entity_type

Nama entity.

Contoh:

- barcode
- material
- site
- user
- role
- permission

---

## entity_id

ID dari entity yang berubah.

Tidak menggunakan Foreign Key karena dapat mengacu ke berbagai tabel.

---

## action

Jenis aksi.

Nilai yang diperbolehkan:

- create
- update
- delete
- restore
- login
- logout
- export
- import

---

## old_values

JSON.

Menyimpan seluruh nilai sebelum perubahan.

Contoh:

{
    "status": "NEW",
    "site": "SITE001"
}

---

## new_values

JSON.

Menyimpan seluruh nilai setelah perubahan.

Contoh:

{
    "status": "OLD",
    "site": "SITE005"
}

---

## ip_address

Alamat IP user.

Nullable.

---

## user_agent

Browser atau Device.

Nullable.

---

# Ketentuan

Audit Log bersifat IMMUTABLE.

Data tidak boleh diubah kembali.

Data tidak boleh dihapus.

Tidak menggunakan softDeletes().

Tidak menggunakan updated_at.

Hanya menggunakan created_at.

---

# Index

Minimal:

- Index user_id
- Index entity_type + entity_id
- Index action
- Index created_at

---

# Yang Tidak Boleh

JANGAN:

- Membuat migration lain.
- Mengubah migration sebelumnya.
- Membuat model.
- Membuat repository.
- Membuat service.
- Membuat controller.
- Membuat API.
- Membuat seeder.
- Membuat factory.

---

# Acceptance Criteria

- Hanya migration audit_logs.
- JSON digunakan untuk old_values dan new_values.
- Tidak ada updated_at.
- Tidak ada softDeletes().
- Foreign Key sesuai spesifikasi.
- Index sesuai spesifikasi.
- Migration dapat dijalankan.

---

# Output

AI wajib melaporkan:

1. Nama migration.
2. Struktur kolom lengkap.
3. Foreign Key.
4. Index.
5. File dibuat.
6. File diubah.
7. Verifikasi.
8. Konfirmasi task lain belum dikerjakan.