# MIGRATION PLAN

## Tujuan

Dokumen ini menjadi acuan urutan pembuatan migration Laravel.

Migration wajib dibuat sesuai urutan karena terdapat relasi Foreign Key antar tabel.

---

# Urutan Migration

## Phase 1 - Authentication

1. users
2. roles
3. permissions
4. role_user
5. permission_role

---

## Phase 2 - Master Data

6. sites
7. material_types
8. material_models
9. materials

---

## Phase 3 - Barcode

10. barcodes
11. barcode_histories

---

## Phase 4 - System

12. audit_logs
13. activity_logs

---

# Aturan Migration

- Setiap tabel wajib memiliki Primary Key.
- Seluruh Foreign Key harus menggunakan constraint.
- Seluruh tabel menggunakan created_at dan updated_at.
- Soft Delete hanya diterapkan pada tabel yang membutuhkannya.
- Penamaan tabel menggunakan bentuk jamak (plural).
- Penamaan kolom menggunakan snake_case.
- Tidak diperbolehkan membuat migration di luar urutan ini tanpa persetujuan.

---

# Catatan

Setelah seluruh migration selesai, baru diperbolehkan membuat:

- Model
- Seeder
- Factory
- Repository
- Service
- API