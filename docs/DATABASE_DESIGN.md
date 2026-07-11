# Database Design

## 1. Gambaran Umum Database

Database aplikasi Barcode Management System dirancang untuk menyimpan data master material, barcode, aktivitas pengguna, dan riwayat perubahan. Tujuan utamanya adalah memastikan integritas data, memudahkan pencarian dan pelacakan, serta mendukung kebutuhan audit dan operasional warehouse, admin, dan engineer.

Database ini akan menjadi fondasi untuk fitur generate barcode, pencatatan aktivitas, histori perubahan, dan manajemen pengguna dengan sistem approval.

---

## 2. Daftar Entitas

### Users
Entitas untuk menyimpan informasi pengguna sistem, termasuk nama, email, status akun, dan relasi ke role.

### Roles
Entitas untuk mengelompokkan hak akses pengguna berdasarkan peran seperti Admin, Warehouse, Field Engineer, dan Asset Management.

### Permissions
Entitas untuk menyimpan hak akses granular yang dapat dikaitkan ke role.

### Sites
Entitas untuk menyimpan lokasi atau site tempat material berada atau dipakai.

### Materials
Entitas utama untuk menyimpan informasi material, termasuk kode material, deskripsi, status, dan relasi ke site, type, dan model.

### Material Types
Entitas untuk mengelompokkan material berdasarkan tipe yang umum digunakan.

### Material Models
Entitas untuk mengelompokkan material berdasarkan model atau varian tertentu.

### Barcodes
Entitas untuk menyimpan informasi barcode yang dihasilkan untuk material. Barcode ID bersifat permanen dan tidak berubah walaupun data material diperbarui.

### Barcode Histories
Entitas untuk mencatat riwayat perubahan terkait barcode, termasuk perubahan data material yang terkait dengan barcode.

### Audit Logs
Entitas untuk mencatat perubahan signifikan pada data yang memerlukan pelacakan audit, misalnya perubahan status, approval, atau operasi administratif.

### Activity Logs
Entitas untuk mencatat aktivitas pengguna dan sistem secara otomatis, seperti create, update, approve, delete, dan export.

### Entitas tambahan yang disarankan
- Approval Requests: untuk menangani alur approval user atau perubahan data.
- Attachments: untuk menyimpan lampiran dokumen pendukung jika dibutuhkan di masa depan.

---

## 3. Relasi Antar Entitas

- Users memiliki banyak Activity Logs.
- Users memiliki banyak Audit Logs.
- Users dapat memiliki satu Role.
- Roles memiliki banyak Users.
- Roles memiliki banyak Permissions melalui relasi many-to-many.
- Sites memiliki banyak Materials.
- Materials memiliki satu Material Type.
- Materials memiliki satu Material Model.
- Materials memiliki banyak Barcodes.
- Barcodes memiliki banyak Barcode Histories.
- Barcodes terkait dengan satu Material.
- Activity Logs dan Audit Logs biasanya terkait dengan Users dan entitas lain yang dipengaruhi.

---

## 4. Prinsip Database

- Soft delete digunakan jika diperlukan untuk menjaga riwayat data.
- Timestamp wajib disertakan pada tabel utama untuk memudahkan audit.
- Barcode ID bersifat permanen dan tidak berubah.
- Serial Number harus unik sesuai kebijakan bisnis.
- Foreign key wajib digunakan untuk menjaga integritas referensi antar entitas.
- Hindari duplikasi data dengan memisahkan data master ke tabel terpisah.
- Gunakan indeks pada kolom yang sering dipakai untuk pencarian dan filter.
- Semua perubahan data penting harus tercatat di log audit atau activity log.
