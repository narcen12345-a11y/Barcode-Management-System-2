import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useCrud } from '../../hooks/useCrud';
import { usePermission } from '../../hooks/usePermission';
import { barcodeService } from '../../services/barcodeService';
import { siteService } from '../../services/siteService';
import { materialService } from '../../services/materialService';
import { columnBuilder } from '../../utils/tableHelpers';
import { PageHeader } from '../../components/ui/PageHeader';
import { TableToolbar } from '../../components/ui/TableToolbar';
import { DataTable } from '../../components/ui/DataTable';
import { Pagination } from '../../components/ui/Pagination';
import { DeleteDialog } from '../../components/ui/DeleteDialog';
import { RestoreDialog } from '../../components/ui/RestoreDialog';
import { FilterBar } from '../../components/ui/FilterBar';

const STATUS_OPTIONS = [
  { value: 'NEW', label: 'NEW (MOS)' },
  { value: 'OLD', label: 'OLD (DISMANTLE)' },
];

export function BarcodeListPage() {
  const navigate = useNavigate();
  const { hasPermission } = usePermission();
  const crud = useCrud(barcodeService, 'barcodes', { perPage: 15 });
  const [deleteTarget, setDeleteTarget] = useState(null);
  const [restoreTarget, setRestoreTarget] = useState(null);

  // Dropdown data for filters
  const [sites, setSites] = useState([]);
  const [materials, setMaterials] = useState([]);

  useEffect(() => {
    siteService.getAllUnpaginated().then((res) => {
      setSites(res.data || res || []);
    });
    materialService.getAllUnpaginated().then((res) => {
      setMaterials(res.data || res || []);
    });
  }, []);

  const columns = columnBuilder()
    .text('barcode_id', 'Barcode ID')
    .text('serial_number', 'Serial Number')
    .custom('material', 'Material', (row) => {
      return row.material?.name || row.material_id || '—';
    })
    .custom('site', 'Site', (row) => {
      return row.site?.name || row.site_id || '—';
    })
    .status('status', 'Status', {
      render: (row) => {
        const colors = {
          NEW: 'text-cyan-400',
          OLD: 'text-amber-400',
        };
        const labels = {
          NEW: 'NEW (MOS)',
          OLD: 'OLD (DISMANTLE)',
        };
        return (
          <span className={`font-medium ${colors[row.status] || 'text-slate-400'}`}>
            {labels[row.status] || row.status}
          </span>
        );
      },
    })
    .text('description', 'Description')
    .date('created_at', 'Dibuat')
    .actions({
      onView: hasPermission('read-barcode') ? (row) => navigate(`/barcodes/${row.id}`) : null,
      onEdit: hasPermission('update-barcode') ? (row) => navigate(`/barcodes/${row.id}/edit`) : null,
      onDelete: hasPermission('delete-barcode') ? (row) => setDeleteTarget(row) : null,
      onRestore: hasPermission('delete-barcode') ? (row) => setRestoreTarget(row) : null,
    })
    .build();

  const filterConfig = [
    {
      key: 'status',
      label: 'Status',
      type: 'select',
      options: STATUS_OPTIONS,
    },
    {
      key: 'site_id',
      label: 'Site',
      type: 'select',
      options: sites.map((s) => ({ value: String(s.id), label: s.name })),
    },
    {
      key: 'material_id',
      label: 'Material',
      type: 'select',
      options: materials.map((m) => ({ value: String(m.id), label: m.name })),
    },
    {
      key: 'is_active',
      label: 'Is Active',
      type: 'select',
      options: [
        { value: '1', label: 'Active' },
        { value: '0', label: 'Inactive' },
      ],
    },
  ];

  return (
    <div>
      <PageHeader title="Barcodes" description="Kelola data barcode" />

      <TableToolbar
        search={crud.search}
        onSearch={crud.setSearch}
        onRefresh={crud.refresh}
        onCreate={hasPermission('create-barcode') ? () => navigate('/barcodes/create') : null}
        createLabel="Tambah Barcode"
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
        emptyTitle="Belum ada barcode"
        emptyDescription="Belum ada data barcode. Klik 'Tambah Barcode' untuk membuat baru."
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
