# PHASE 1 - TASK 1.3
# Coding & Folder Convention

## Tujuan

Membangun standar struktur project agar seluruh pengembangan berikutnya konsisten, mudah dipelihara, dan mengikuti prinsip Clean Architecture.

---

## Yang harus dilakukan

Baca seluruh folder:

- .ai
- docs

Kemudian lakukan hal berikut.

### 1. Tambahkan file README.md pada setiap folder berikut apabila belum tersedia.

app/Actions
app/DTOs
app/Enums
app/Exceptions
app/Helpers
app/Interfaces
app/Policies
app/Repositories
app/Rules
app/Services
app/Traits
app/ValueObjects
app/Support
app/Observers
app/Events
app/Listeners
app/Jobs
app/Notifications
app/Mail

Isi setiap README.md dengan penjelasan singkat mengenai fungsi folder tersebut.

---

### 2. Buat dokumentasi coding standard.

Lokasi:

docs/CODING_STANDARD.md

Isi minimal:

- Naming Convention
- Folder Convention
- File Convention
- Controller Convention
- Service Convention
- Repository Convention
- DTO Convention
- Enum Convention
- Validation Convention
- API Response Convention
- Error Handling Convention

---

### 3. Buat dokumentasi folder structure.

Lokasi:

docs/FOLDER_STRUCTURE.md

Jelaskan:

- Struktur backend
- Fungsi setiap folder
- Alur data dari Request sampai Response

---

## Yang TIDAK BOLEH dilakukan

Jangan membuat:

- Login
- Controller
- Route baru
- Migration baru
- Seeder baru
- Business Logic
- API
- Database baru

---

## Setelah selesai

Berikan laporan:

- File apa saja yang dibuat
- Penjelasan singkat setiap file
- Pastikan project tetap dapat dijalankan

Kemudian berhenti.

## Definition of Done

Semua README dibuat.

CODING_STANDARD.md selesai.

FOLDER_STRUCTURE.md selesai.

Tidak ada perubahan pada business logic.