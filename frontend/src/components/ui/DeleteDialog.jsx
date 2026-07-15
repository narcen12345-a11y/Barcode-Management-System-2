import { ConfirmDialog } from './ConfirmDialog';

/**
 * DeleteDialog — pre-configured confirmation dialog for delete actions.
 *
 * Usage:
 *   <DeleteDialog
 *     open={deleteDialog.open}
 *     onClose={() => setDeleteDialog({ open: false })}
 *     onConfirm={() => crud.remove(deleteDialog.row.id)}
 *     loading={crud.deleting}
 *   />
 */
export function DeleteDialog({ open, onClose, onConfirm, loading, title, description }) {
  return (
    <ConfirmDialog
      open={open}
      onClose={onClose}
      onConfirm={onConfirm}
      title={title || 'Hapus Data'}
      description={description || 'Data yang dihapus akan dipindahkan ke tempat sampah dan dapat dipulihkan kembali.'}
      confirmText={loading ? 'Menghapus...' : 'Ya, Hapus'}
      cancelText="Batal"
      variant="danger"
    />
  );
}
