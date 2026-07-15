# CRUD Engine — Developer Guide

## Overview

The CRUD engine provides reusable infrastructure for building consistent CRUD pages across all modules. It eliminates boilerplate by providing:

- **BaseCrudService** — generic API service for any resource
- **useCrud** — hook for list pages (pagination, search, filters, sorting, delete, restore)
- **useFormSubmit** — hook for create/edit forms (validation, loading, toast, cache invalidation)
- **Column Builder** — fluent API for table column definitions
- **Shared Components** — DeleteDialog, RestoreDialog, FilterBar, TableToolbar

---

## How to Create a New CRUD Page

### Step 1: Create a Service

```js
// src/services/userService.js
import { BaseCrudService } from './BaseCrudService';

export const userService = new BaseCrudService('/users');
```

### Step 2: Create a List Page

```jsx
// src/pages/users/UserListPage.jsx
import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useCrud } from '../../hooks/useCrud';
import { usePermission } from '../../hooks/usePermission';
import { userService } from '../../services/userService';
import { columnBuilder } from '../../utils/tableHelpers';
import { PageHeader } from '../../components/ui/PageHeader';
import { TableToolbar } from '../../components/ui/TableToolbar';
import { DataTable } from '../../components/ui/DataTable';
import { Pagination } from '../../components/ui/Pagination';
import { DeleteDialog } from '../../components/ui/DeleteDialog';

export function UserListPage() {
  const navigate = useNavigate();
  const { hasPermission } = usePermission();
  const crud = useCrud(userService, 'users', { perPage: 15 });
  const [deleteTarget, setDeleteTarget] = useState(null);

  const columns = columnBuilder()
    .text('username', 'Username')
    .text('email', 'Email')
    .text('full_name', 'Nama Lengkap')
    .status('status', 'Status')
    .date('created_at', 'Dibuat')
    .actions({
      onEdit: (row) => navigate(`/users/${row.id}/edit`),
      onDelete: hasPermission('delete-user') ? (row) => setDeleteTarget(row) : null,
    })
    .build();

  return (
    <div>
      <PageHeader title="Users" description="Kelola data pengguna" />
      <TableToolbar
        search={crud.search}
        onSearch={crud.setSearch}
        onRefresh={crud.refresh}
        onCreate={() => navigate('/users/create')}
        createLabel="Tambah User"
        createPermission="create-user"
        hasPermission={hasPermission}
      />
      <DataTable
        columns={columns}
        data={crud.rows}
        loading={crud.loading}
        emptyTitle="Belum ada user"
        emptyDescription="Belum ada data pengguna yang tersedia."
      />
      {crud.meta && (
        <div className="mt-4">
          <Pagination meta={crud.meta} onPageChange={crud.setPage} />
        </div>
      )}
      <DeleteDialog
        open={!!deleteTarget}
        onClose={() => setDeleteTarget(null)}
        onConfirm={() => {
          crud.remove(deleteTarget.id);
          setDeleteTarget(null);
        }}
        loading={crud.deleting}
      />
    </div>
  );
}
```

### Step 3: Create a Form Page

```jsx
// src/pages/users/UserFormPage.jsx
import { useNavigate, useParams } from 'react-router-dom';
import { useEffect } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useFormSubmit } from '../../hooks/useFormSubmit';
import { userService } from '../../services/userService';
import { PageHeader } from '../../components/ui/PageHeader';
import { Spinner } from '../../components/ui/Spinner';

const schema = z.object({
  username: z.string().min(1, 'Username wajib diisi'),
  email: z.string().email('Email tidak valid'),
  full_name: z.string().min(1, 'Nama lengkap wajib diisi'),
  password: z.string().min(8, 'Password minimal 8 karakter').optional(),
});

export function UserFormPage() {
  const { id } = useParams();
  const isEdit = !!id;
  const navigate = useNavigate();

  const form = useFormSubmit({
    service: userService,
    method: isEdit ? 'update' : 'create',
    id,
    queryKey: 'users',
    onSuccess: () => navigate('/users'),
  });

  const { register, handleSubmit, formState: { errors }, reset } = useForm({
    resolver: zodResolver(schema),
  });

  // Load existing data for edit
  useEffect(() => {
    if (isEdit) {
      userService.getById(id).then((res) => {
        const data = res.data || res;
        reset(data);
      });
    }
  }, [id, isEdit, reset]);

  const onSubmit = async (data) => {
    // Remove password if empty on edit
    if (isEdit && !data.password) {
      delete data.password;
    }
    await form.submit(data);
  };

  return (
    <div>
      <PageHeader
        title={isEdit ? 'Edit User' : 'Tambah User'}
        description={isEdit ? 'Perbarui data pengguna' : 'Buat pengguna baru'}
      />
      <form onSubmit={handleSubmit(onSubmit)} className="max-w-lg space-y-4">
        <div>
          <label className="mb-1 block text-sm text-slate-300">Username</label>
          <input {...register('username')} className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-sm text-slate-100" />
          {errors.username && <p className="mt-1 text-xs text-red-400">{errors.username.message}</p>}
          {form.errors?.username && <p className="mt-1 text-xs text-red-400">{form.errors.username}</p>}
        </div>
        {/* ... more fields ... */}
        <button type="submit" disabled={form.loading} className="rounded-lg bg-cyan-500 px-4 py-2 text-white">
          {form.loading ? 'Menyimpan...' : 'Simpan'}
        </button>
      </form>
    </div>
  );
}
```

### Step 4: Add Routes

```jsx
// src/routes/index.jsx — add inside the protected layout children
{
  path: 'users',
  element: <UserListPage />,
},
{
  path: 'users/create',
  element: <UserFormPage />,
},
{
  path: 'users/:id/edit',
  element: <UserFormPage />,
},
```

---

## Expected Folder Structure

```
src/
├── api/
│   └── client.js              # Axios instance
├── services/
│   ├── authService.js          # Auth API calls
│   ├── BaseCrudService.js      # Generic CRUD service
│   ├── userService.js          # User CRUD service
│   ├── siteService.js          # Site CRUD service
│   ├── roleService.js          # Role CRUD service
│   ├── materialTypeService.js  # Material Type CRUD service
│   ├── materialModelService.js # Material Model CRUD service
│   ├── materialService.js      # Material CRUD service
│   └── barcodeService.js       # Barcode CRUD service
├── hooks/
│   ├── useCrud.js              # Generic list hook
│   ├── useFormSubmit.js        # Generic form hook
│   └── usePermission.js        # Permission checking
├── components/
│   ├── ui/                     # Reusable UI components
│   │   ├── DataTable.jsx
│   │   ├── Pagination.jsx
│   │   ├── DeleteDialog.jsx
│   │   ├── RestoreDialog.jsx
│   │   ├── FilterBar.jsx
│   │   ├── TableToolbar.jsx
│   │   ├── SearchInput.jsx
│   │   ├── PageHeader.jsx
│   │   ├── ConfirmDialog.jsx
│   │   ├── Spinner.jsx
│   │   ├── Loading.jsx
│   │   └── EmptyState.jsx
│   └── ProtectedRoute.jsx
├── pages/
│   ├── LoginPage.jsx
│   ├── DashboardPage.jsx
│   ├── NotFoundPage.jsx
│   └── users/                  # Example: User CRUD pages
│       ├── UserListPage.jsx
│       └── UserFormPage.jsx
├── layouts/
│   ├── MainLayout.jsx
│   ├── Sidebar.jsx
│   └── Topbar.jsx
├── contexts/
│   └── AuthContext.jsx
├── routes/
│   └── index.jsx
├── utils/
│   ├── cn.js
│   ├── queryBuilder.js
│   ├── errorParser.js
│   ├── permissionHelper.js
│   └── tableHelpers.jsx
├── auth/
│   └── storage.js
└── main.jsx
```

---

## How BaseCrudService Works

```
BaseCrudService('/users')
    │
    ├── getAll(params)      → GET  /users?page=1&per_page=15&search=...
    ├── getAllUnpaginated() → GET  /users/all
    ├── getById(id)         → GET  /users/{id}
    ├── create(payload)     → POST /users
    ├── update(id, payload) → PUT  /users/{id}
    ├── delete(id)          → DELETE /users/{id}
    └── restore(id)         → POST /users/{id}/restore
```

Each method returns the full API response. The `useCrud` hook handles extracting `data` and `meta` from various response shapes.

---

## API Response Shape Compatibility

The CRUD engine handles these Laravel response shapes:

**Paginated (from ResourceCollection):**
```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  }
}
```

**Single resource:**
```json
{
  "data": { "id": 1, "name": "..." }
}
```

**Direct array (from ->all()):**
```json
[
  { "id": 1, "name": "..." }
]
```
