import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useCrud } from '../../hooks/useCrud';
import { usePermission } from '../../hooks/usePermission';
import { materialService } from '../../services/materialService';
import { columnBuilder } from '../../utils/tableHelpers';
import { PageHeader } from '../../components/ui/PageHeader';
import { TableToolbar } from '../../components/ui/TableToolbar';
import { DataTable } from '../../components/ui/DataTable';
import { Pagination } from '../../components/ui/Pagination';
import { DeleteDialog } from '../../components/ui/DeleteDialog';
import { RestoreDialog } from '../../components/ui/RestoreDialog';
import { FilterBar } from '../../components/ui/FilterBar';

export function MaterialListPage() {
  const navigate = useNavigate();
  const { hasPermission } = usePermission();
  const crud = useCrud(materialService, 'materials', { perPage: 15 });
  const [deleteTarget, setDeleteTarget] = useState(null);
  const [restoreTarget, setRestoreTarget] = useState(null);

  const columns = columnBuilder()
    .text('material_code', 'Material Code')
    .text('name', 'Name')
    .custom('material_type', 'Material Type', (row) => {
      return row.material_type?.name || row.material_type_id || '—';
    })
    .custom('material_model', 'Material Model', (row) => {
      return row.material_model?.name || row.material_model_id || '—';
    })
    .text('description', 'Description')
    .status('is_active', 'Status', {
      render: (row) => {
        const label = row.is_active ? 'active' : 'inactive';
        return <span className="capitalize">{label}</span>;
      },
    })
    .date('created_at', 'Dibuat')
    .actions({
      onEdit: hasPermission('update-material') ? (row) => navigate(`/materials/${row.id}/edit`) : null,
      onDelete: hasPermission('delete-material') ? (row) => setDeleteTarget(row) : null,
      onRestore: hasPermission('delete-material') ? (row) => setRestoreTarget(row) : null,
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
      <PageHeader title="Materials" description="Kelola data material" />

      <TableToolbar
        search={crud.search}
        onSearch={crud.setSearch}
        onRefresh={crud.refresh}
        onCreate={hasPermission('create-material') ? () => navigate('/materials/create') : null}
        createLabel="Tambah Material"
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
        emptyTitle="Belum ada material"
        emptyDescription="Belum ada data material. Klik 'Tambah Material' untuk membuat baru."
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
