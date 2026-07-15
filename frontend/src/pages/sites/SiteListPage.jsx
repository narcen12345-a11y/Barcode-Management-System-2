import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useCrud } from '../../hooks/useCrud';
import { usePermission } from '../../hooks/usePermission';
import { siteService } from '../../services/siteService';
import { columnBuilder } from '../../utils/tableHelpers';
import { PageHeader } from '../../components/ui/PageHeader';
import { TableToolbar } from '../../components/ui/TableToolbar';
import { DataTable } from '../../components/ui/DataTable';
import { Pagination } from '../../components/ui/Pagination';
import { DeleteDialog } from '../../components/ui/DeleteDialog';
import { RestoreDialog } from '../../components/ui/RestoreDialog';
import { FilterBar } from '../../components/ui/FilterBar';

export function SiteListPage() {
  const navigate = useNavigate();
  const { hasPermission } = usePermission();
  const crud = useCrud(siteService, 'sites', { perPage: 15 });
  const [deleteTarget, setDeleteTarget] = useState(null);
  const [restoreTarget, setRestoreTarget] = useState(null);

  const columns = columnBuilder()
    .text('site_id', 'Site ID')
    .text('site_name', 'Site Name')
    .text('region', 'Region')
    .text('address', 'Address')
    .status('is_active', 'Status', {
      render: (row) => {
        const label = row.is_active ? 'active' : 'inactive';
        return <span className="capitalize">{label}</span>;
      },
    })
    .date('created_at', 'Dibuat')
    .actions({
      onEdit: hasPermission('update-site') ? (row) => navigate(`/sites/${row.id}/edit`) : null,
      onDelete: hasPermission('delete-site') ? (row) => setDeleteTarget(row) : null,
      onRestore: hasPermission('delete-site') ? (row) => setRestoreTarget(row) : null,
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
      <PageHeader title="Sites" description="Kelola data lokasi / site" />

      <TableToolbar
        search={crud.search}
        onSearch={crud.setSearch}
        onRefresh={crud.refresh}
        onCreate={hasPermission('create-site') ? () => navigate('/sites/create') : null}
        createLabel="Tambah Site"
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
        emptyTitle="Belum ada site"
        emptyDescription="Belum ada data site yang tersedia. Klik 'Tambah Site' untuk membuat baru."
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
