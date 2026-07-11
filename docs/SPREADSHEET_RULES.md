# SPREADSHEET RULES

## Tujuan

Dokumen ini mengatur seluruh aturan bisnis mengenai pencatatan otomatis Barcode ke Spreadsheet.

---

## Rule 001 - Pencatatan Otomatis

Setiap Barcode yang berhasil dibuat wajib otomatis tercatat pada Spreadsheet.

---

## Rule 002 - Waktu Pencatatan

Data dicatat setelah proses Save berhasil dilakukan.

Apabila proses Save gagal, Spreadsheet tidak boleh diperbarui.

---

## Rule 003 - Data Yang Dicatat

Spreadsheet minimal berisi:

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

---

## Rule 004 - Link Barcode

Link Barcode dibuat setelah seluruh data berhasil disimpan.

Link harus selalu mengarah ke halaman Detail Barcode.

---

## Rule 005 - Update Spreadsheet

Apabila informasi Barcode berubah, baris yang sama pada Spreadsheet harus diperbarui.

Sistem tidak boleh membuat baris baru.

---

## Rule 006 - Konsistensi Data

Data pada Spreadsheet harus selalu sama dengan data yang terdapat pada aplikasi.

---

## Rule 007 - Penghapusan Barcode

Apabila Barcode di-Soft Delete, status pada Spreadsheet berubah menjadi "Inactive".

Data tidak boleh dihapus.

---

## Rule 008 - Restore Barcode

Apabila Barcode di-Restore, Spreadsheet diperbarui kembali menjadi aktif.

---

## Rule 009 - Sinkronisasi

Apabila terjadi kegagalan sinkronisasi, sistem harus mencatat kegagalan tersebut pada Audit Log dan menyediakan mekanisme sinkronisasi ulang.

---

## Rule 010 - Hak Akses

User hanya dapat melihat data sesuai hak aksesnya.

Admin dapat melihat seluruh data Spreadsheet.