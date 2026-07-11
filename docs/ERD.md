# ENTITY RELATIONSHIP DIAGRAM (ERD)

## Tujuan

Dokumen ini mendefinisikan hubungan antar tabel pada Barcode Management System sebagai acuan implementasi database.

---

# Relasi

## Users

Users memiliki satu atau lebih Role.

```
Users
    │
    ├───────────────< Role_User >───────────────┐
                                                │
                                                ▼
                                             Roles
```

---

## Roles

Role memiliki banyak Permission.

```
Roles
    │
    ├──────────────< Permission_Role >──────────────┐
                                                    │
                                                    ▼
                                              Permissions
```

---

## Sites

Satu Site dapat memiliki banyak Barcode.

```
Sites
    │
    ├──────────────< Barcodes
```

---

## Materials

Satu Material dapat digunakan oleh banyak Barcode.

```
Materials
    │
    ├──────────────< Barcodes
```

---

## Material Types

Satu Type dimiliki banyak Material.

```
Material Types
    │
    ├──────────────< Materials
```

---

## Material Models

Satu Model dimiliki banyak Material.

```
Material Models
    │
    ├──────────────< Materials
```

---

## Barcodes

Satu Barcode memiliki:

- satu Site
- satu Material
- satu Type
- satu Model

dan memiliki banyak History.

```
Barcodes
      │
      ├──────────────< Barcode Histories
```

---

## Audit Log

Satu User dapat memiliki banyak Audit Log.

```
Users
    │
    ├──────────────< Audit Logs
```

---

## Activity Log

Satu User dapat memiliki banyak Activity Log.

```
Users
    │
    ├──────────────< Activity Logs
```

---

# Diagram Ringkas

```
Users
 │
 ├────< Role_User >──── Roles ────< Permission_Role >──── Permissions

Sites
 │
 ├────< Barcodes >──── Materials
                       │
                       ├──── Material Types
                       │
                       └──── Material Models

Barcodes
 │
 ├────< Barcode Histories
 │
 ├────< Audit Logs
 │
 └────< Activity Logs
```

---

# Catatan

Seluruh Foreign Key akan mengikuti relasi yang dijelaskan pada dokumen ini.

Apabila terdapat perubahan struktur database, dokumen ini harus diperbarui terlebih dahulu sebelum Migration dibuat.