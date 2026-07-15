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
import { RestoreDialog } from '../../components/ui/RestoreDialog';
import { FilterBar } from '../../components/ui/FilterBar';
import { ConfirmDialog } from '../../components/ui/ConfirmDialog';
import { toast } from 'sonner';

export function UserListPage() {
  const navigate = useNavigate();
  const { hasPermission } = usePermission();
  const crud = useCrud(userService, 'users', { perPage: 15 });
  const [deleteTarget, setDeleteTarget] = useState(null);
  const [restoreTarget, setRestoreTarget] = useState(null);
  const [activateTarget, setActivateTarget] = useState(null);
  const [deactivateTarget, setDeactivateTarget] = useState(null);
  const [resetPwTarget, setResetPwTarget] = useState(null);
  const [actionLoading, setActionLoading] = useState(false);

  const handleActivate = async (id) => {
    setActionLoading(true);
    try {
      await userService.activate(id);
      toast.success('User berhasil diaktifkan');
      crud.refresh();
    } catch {
      toast.error('Gagal mengaktifkan user');
    } finally {
      setActionLoading(false);
      setActivateTarget(null);
    }
  };

  const handleDeactivate = async (id) => {
    setActionLoading(true);
    try {
      await userService.deactivate(id);
      toast.success('User berhasil dinonaktifkan');
      crud.refresh();
    } catch {
      toast.error('Gagal menonaktifkan user');
    } finally {
      setActionLoading(false);
      setDeactivateTarget(null);
    }
  };

  const handleResetPassword = async (id) => {
    setActionLoading(true);
    try {
      const res = await userService.resetPassword(id);
      const newPassword = res?.data?.new_password || 'password123';
      toast.success(`Password berhasil di-reset. Password baru: ${newPassword}`, {
        duration: 10000,
      });
    } catch {
      toast.error('Gagal mereset password');
    } finally {
      setActionLoading(false);
      setResetPwTarget(null);
    }
  };

  const columns = columnBuilder()
    .text('username', 'Username')
    .text('full_name', 'Full Name')
    .text('email', 'Email')
    .custom('roles', 'Roles', (row) => {
      const roles = row.roles || [];
      if (roles.length === 0) return <span className="text-slate-500">—</span>;
      return (
        <div className="flex flex-wrap gap-1">
          {roles.map((r) => (
            <span
              key={r.id}
              className="inline-block rounded bg-slate-700 px-2 py-0.5 text-xs text-slate-300"
            >
              {r.display_name || r.name}
            </span>
          ))}
        </div>
      );
    })
    .status('status', 'Status', {
      render: (row) => {
        const status = row.status || (row.is_active ? 'active' : 'inactive');
        const colors = {
          active: 'text-green-400',
          inactive: 'text-red-400',
          pending: 'text-yellow-400',
          suspended: 'text-orange-400',
        };
        return (
          <span className={`capitalize ${colors[status] || 'text-slate-400'}`}>
            {status}
          </span>
        );
      },
    })
    .date('created_at', 'Dibuat')
    .actions({
      onEdit: hasPermission('update-user') ? (row) => navigate(`/users/${row.id}/edit`) : null,
      onDelete: hasPermission('delete-user') ? (row) => setDeleteTarget(row) : null,
      onRestore: hasPermission('delete-user') ? (row) => setRestoreTarget(row) : null,
      extraActions: [
        hasPermission('activate-user') && {
          label: 'Aktifkan',
          onClick: (row) => setActivateTarget(row),
          show: (row) => !row.is_active || row.status === 'inactive',
        },
        hasPermission('deactivate-user') && {
          label: 'Nonaktifkan',
          onClick: (row) => setDeactivateTarget(row),
          show: (row) => row.is_active && row.status !== 'inactive',
        },
        hasPermission('reset-password') && {
          label: 'Reset Password',
          onClick: (row) => setResetPwTarget(row),
          show: () => true,
        },
      ].filter(Boolean),
    })
    .build();

  const filterConfig = [
    {
      key: 'status',
      label: 'Status',
      type: 'select',
      options: [
        { value: 'active', label: 'Active' },
        { value: 'inactive', label: 'Inactive' },
        { value: 'pending', label: 'Pending' },
        { value: 'suspended', label: 'Suspended' },
      ],
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
      <PageHeader title="Users" description="Kelola pengguna sistem" />

      <TableToolbar
        search={crud.search}
        onSearch={crud.setSearch}
        onRefresh={crud.refresh}
        onCreate={hasPermission('create-user') ? () => navigate('/users/create') : null}
        createLabel="Tambah User"
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
        emptyTitle="Belum ada user"
        emptyDescription="Belum ada data pengguna. Klik 'Tambah User' untuk membuat baru."
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

      <ConfirmDialog
        open={!!activateTarget}
        onClose={() => setActivateTarget(null)}
        onConfirm={() => handleActivate(activateTarget.id)}
        title="Aktifkan User"
        message={`Yakin ingin mengaktifkan user "${activateTarget?.full_name || activateTarget?.username}"?`}
        confirmLabel="Aktifkan"
        loading={actionLoading}
      />

      <ConfirmDialog
        open={!!deactivateTarget}
        onClose={() => setDeactivateTarget(null)}
        onConfirm={() => handleDeactivate(deactivateTarget.id)}
        title="Nonaktifkan User"
        message={`Yakin ingin menonaktifkan user "${deactivateTarget?.full_name || deactivateTarget?.username}"?`}
        confirmLabel="Nonaktifkan"
        loading={actionLoading}
      />

      <ConfirmDialog
        open={!!resetPwTarget}
        onClose={() => setResetPwTarget(null)}
        onConfirm={() => handleResetPassword(resetPwTarget.id)}
        title="Reset Password"
        message={`Yakin ingin mereset password user "${resetPwTarget?.full_name || resetPwTarget?.username}"?`}
        confirmLabel="Reset"
        loading={actionLoading}
      />
    </div>
  );
}
