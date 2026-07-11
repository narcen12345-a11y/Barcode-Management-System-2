# PROJECT OVERVIEW

Project ini adalah sistem manajemen barcode material.

Tujuan utamanya:

- Generate Barcode
- Menyimpan seluruh data material
- Memungkinkan update data tanpa mengganti barcode
- Menyimpan histori
- Mencatat seluruh aktivitas
- Menyediakan dashboard
- Menyediakan filter
- Menyediakan export data

Seluruh data harus tersimpan dalam PostgreSQL.

Barcode harus menggunakan Code128.

Barcode ID bersifat permanen dan tidak berubah walaupun informasi material diperbarui.

Nomor Serial (SN) harus unik sesuai kebijakan bisnis.