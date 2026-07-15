import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useCrud } from '../../hooks/useCrud';
import { usePermission } from '../../hooks/usePermission';
import { materialTypeService } from '../../services/materialTypeService';
import { columnBuilder } from '../../utils/tableHelpers';
import { PageHeader } from '../../components/ui/PageHeader';
import { TableToolbar } from '../../components/ui/TableToolbar';
import { DataTable } from '../../components/ui/DataTable';
import { Pagination } from '../../components/ui/Pagination';
import { DeleteDialog } from '../../components/ui/DeleteDialog';
import { RestoreDialog } from '../../components/ui/RestoreDialog';
import { FilterBar } from '../../components/ui/FilterBar';

export function MaterialTypeListPage() {
  const navigate = useNavigate();
  const { hasPermission } = usePermission();
  const crud = useCrud(materialTypeService, 'material-types', { perPage: 15 });
  const [deleteTarget, setDeleteTarget] = useState(null);
  const [restoreTarget, setRestoreTarget] = useState(null);

  const columns = columnBuilder()
    .text('name', 'Name')
    .text('description', 'Description')
    .status('is_active', 'Status', {
      render: (row) => {
        const label = row.is_active ? 'active' : 'inactive';
        return <span className="capitalize">{label}</span>;
      },
    })
    .date('created_at', 'Dibuat')
    .actions({
      onEdit: hasPermission('update-material-type') ? (row) => navigate(`/material-types/${row.id}/edit`) : null,
      onDelete: hasPermission('delete-material-type') ? (row) => setDeleteTarget(row) : null,
      onRestore: hasPermission('delete-material-type') ? (row) => setRestoreTarget(row) : null,
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
      <PageHeader title="Material Types" description="Kelola tipe material" />

      <TableToolbar
        search={crud.search}
        onSearch={crud.setSearch}
        onRefresh={crud.refresh}
        onCreate={hasPermission('create-material-type') ? () => navigate('/material-types/create') : null}
        createLabel="Tambah Material Type"
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
        emptyTitle="Belum ada material type"
        emptyDescription="Belum ada data tipe material. Klik 'Tambah Material Type' untuk membuat baru."
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
