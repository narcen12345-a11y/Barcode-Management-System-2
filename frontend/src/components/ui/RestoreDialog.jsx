import { ConfirmDialog } from './ConfirmDialog';

/**
 * RestoreDialog — pre-configured confirmation dialog for restore actions.
 *
 * Usage:
 *   <RestoreDialog
 *     open={restoreDialog.open}
 *     onClose={() => setRestoreDialog({ open: false })}
 *     onConfirm={() => crud.restore(restoreDialog.row.id)}
 *     loading={crud.restoring}
 *   />
 */
export function RestoreDialog({ open, onClose, onConfirm, loading, title, description }) {
  return (
    <ConfirmDialog
      open={open}
      onClose={onClose}
      onConfirm={onConfirm}
      title={title || 'Pulihkan Data'}
      description={description || 'Data akan dikembalikan ke daftar aktif.'}
      confirmText={loading ? 'Memulihkan...' : 'Ya, Pulihkan'}
      cancelText="Batal"
      variant="default"
    />
  );
}
