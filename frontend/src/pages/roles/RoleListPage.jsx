import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useCrud } from '../../hooks/useCrud';
import { usePermission } from '../../hooks/usePermission';
import { roleService } from '../../services/roleService';
import { columnBuilder } from '../../utils/tableHelpers';
import { PageHeader } from '../../components/ui/PageHeader';
import { TableToolbar } from '../../components/ui/TableToolbar';
import { DataTable } from '../../components/ui/DataTable';
import { Pagination } from '../../components/ui/Pagination';
import { DeleteDialog } from '../../components/ui/DeleteDialog';
import { RestoreDialog } from '../../components/ui/RestoreDialog';
import { FilterBar } from '../../components/ui/FilterBar';

export function RoleListPage() {
  const navigate = useNavigate();
  const { hasPermission } = usePermission();
  const crud = useCrud(roleService, 'roles', { perPage: 15 });
  const [deleteTarget, setDeleteTarget] = useState(null);
  const [restoreTarget, setRestoreTarget] = useState(null);

  const columns = columnBuilder()
    .text('name', 'Name')
    .text('display_name', 'Display Name')
    .text('description', 'Description')
    .custom('permissions', 'Permissions', (row) => {
      const perms = row.permissions || [];
      if (perms.length === 0) return <span className="text-slate-500">—</span>;
      return (
        <div className="flex flex-wrap gap-1">
          {perms.slice(0, 3).map((p) => (
            <span
              key={p.id}
              className="inline-block rounded bg-slate-700 px-2 py-0.5 text-xs text-slate-300"
            >
              {p.display_name || p.name}
            </span>
          ))}
          {perms.length > 3 && (
            <span className="text-xs text-slate-500">+{perms.length - 3} lagi</span>
          )}
        </div>
      );
    })
    .status('is_active', 'Status', {
      render: (row) => {
        const label = row.is_active ? 'active' : 'inactive';
        return <span className="capitalize">{label}</span>;
      },
    })
    .date('created_at', 'Dibuat')
    .actions({
      onEdit: hasPermission('update-role') ? (row) => navigate(`/roles/${row.id}/edit`) : null,
      onDelete: hasPermission('delete-role') ? (row) => setDeleteTarget(row) : null,
      onRestore: hasPermission('delete-role') ? (row) => setRestoreTarget(row) : null,
    })
    .build();

  const filterConfig = [
    {
      key: 'is_active',
      label: 'Status',
      type: 'select',
      options: [
        { value: '1', label: 'Active' },
        { value: '0', label: 'Inactive' },
      ],
    },
  ];

  return (
    <div>
      <PageHeader title="Roles" description="Kelola peran pengguna" />

      <TableToolbar
        search={crud.search}
        onSearch={crud.setSearch}
        onRefresh={crud.refresh}
        onCreate={hasPermission('create-role') ? () => navigate('/roles/create') : null}
        createLabel="Tambah Role"
        hasPermission={hasPermission}
      />

      <FilterBar
        filters={filterConfig}
        values={crud.filters}
        onChange={(key, value) => crud.setFilters({ [key]: value })}
        onReset={crud.resetFilters}
        className="mb-4"
      />

      <DataTable
        columns={columns}
        data={crud.rows}
        loading={crud.loading}
        emptyTitle="Belum ada role"
        emptyDescription="Belum ada data peran. Klik 'Tambah Role' untuk membuat baru."
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

      <RestoreDialog
        open={!!restoreTarget}
        onClose={() => setRestoreTarget(null)}
        onConfirm={() => {
          crud.restore(restoreTarget.id);
          setRestoreTarget(null);
        }}
        loading={crud.restoring}
      />
    </div>
  );
}
