# Coding Standard

## Naming Convention
- Gunakan PascalCase untuk class, enum, interface, trait, dan DTO.
- Gunakan camelCase untuk method, property, dan variabel.
- Gunakan snake_case untuk nama file jika diperlukan oleh framework, namun tetap konsisten.

## Folder Convention
- Kelompokkan kode berdasarkan responsibility, misalnya Actions, Services, Repositories, DTOs, dan Support.
- Hindari meletakkan semua class di root folder yang sama.

## File Convention
- Setiap class ditempatkan pada file terpisah dengan nama yang mencerminkan fungsi class.
- Gunakan file README.md pada folder penampung untuk menjelaskan tujuan folder.

## Controller Convention
- Controller hanya menerima request, memanggil service, dan mengembalikan response.
- Hindari menulis logika bisnis di controller.

## Service Convention
- Service berisi logika domain atau orkestrasi proses bisnis.
- Service sebaiknya terfokus pada satu tanggung jawab.

## Repository Convention
- Repository digunakan untuk akses data dan abstraksi database.
- Service tidak langsung berinteraksi dengan query detail jika repository sudah tersedia.

## DTO Convention
- DTO digunakan untuk memindahkan data antar layer.
- Hindari mengirim model domain secara langsung ke layer yang berbeda tanpa batasan yang jelas.

## Enum Convention
- Enum digunakan untuk nilai yang terbatas dan memiliki makna tetap.
- Hindari menggunakan string literal berulang untuk status atau tipe.

## Validation Convention
- Validasi dilakukan di layer yang sesuai sebelum data diproses lebih lanjut.
- Gunakan aturan validasi yang jelas dan dapat diuji.

## API Response Convention
- Respons API harus konsisten, mudah diprediksi, dan terstruktur.
- Sertakan status, pesan, dan data jika perlu.

## Error Handling Convention
- Gunakan exception kustom untuk error domain yang perlu ditangani secara spesifik.
- Hindari menampilkan detail teknis yang sensitif ke client.
