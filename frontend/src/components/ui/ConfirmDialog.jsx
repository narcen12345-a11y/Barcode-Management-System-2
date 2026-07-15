import { AlertTriangle, X } from 'lucide-react';
import { cn } from '../../utils/cn';

export function ConfirmDialog({ open, onClose, onConfirm, title, description, confirmText, cancelText, variant }) {
  if (!open) return null;

  const confirmButtonClass =
    variant === 'danger'
      ? 'bg-red-600 hover:bg-red-700'
      : 'bg-cyan-500 hover:bg-cyan-600';

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
      <div className="mx-4 w-full max-w-md rounded-xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
        <div className="flex items-start justify-between">
          <div className="flex items-center gap-3">
            <div className="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800">
              <AlertTriangle className="h-5 w-5 text-amber-400" />
            </div>
            <h3 className="text-lg font-semibold text-slate-100">{title || 'Konfirmasi'}</h3>
          </div>
          <button onClick={onClose} className="text-slate-500 hover:text-slate-300">
            <X className="h-5 w-5" />
          </button>
        </div>
        <p className="mt-3 text-sm text-slate-400">{description || 'Apakah Anda yakin?'}</p>
        <div className="mt-6 flex justify-end gap-3">
          <button
            onClick={onClose}
            className="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800"
          >
            {cancelText || 'Batal'}
          </button>
          <button
            onClick={onConfirm}
            className={cn('rounded-lg px-4 py-2 text-sm font-medium text-white transition', confirmButtonClass)}
          >
            {confirmText || 'Ya, Lanjutkan'}
          </button>
        </div>
      </div>
    </div>
  );
}
