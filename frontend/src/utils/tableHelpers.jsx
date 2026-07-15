import { Pencil, Trash2, RotateCcw } from 'lucide-react';
import { cn } from './cn';

/**
 * Column Builder — create table column definitions.
 *
 * Usage:
 *   const columns = columnBuilder()
 *     .text('name', 'Nama')
 *     .status('status', 'Status')
 *     .date('created_at', 'Dibuat')
 *     .actions({ onEdit, onDelete })
 *     .build();
 */
export function columnBuilder() {
  const cols = [];

  const api = {
    /**
     * Simple text column.
     */
    text(key, label, options = {}) {
      cols.push({
        key,
        label,
        ...options,
        render: options.render || ((row) => row[key] ?? '—'),
      });
      return api;
    },

    /**
     * Status badge column.
     * Expects row[key] to be a string like 'active', 'inactive', etc.
     */
    status(key, label, options = {}) {
      cols.push({
        key,
        label,
        ...options,
        render: (row) => <StatusBadge value={row[key]} />,
      });
      return api;
    },

    /**
     * Date/time column with formatting.
     */
    date(key, label, options = {}) {
      cols.push({
        key,
        label,
        ...options,
        render: (row) => formatDate(row[key]),
      });
      return api;
    },

    /**
     * Custom render column.
     */
    custom(key, label, render, options = {}) {
      cols.push({ key, label, render, ...options });
      return api;
    },

    /**
     * Actions column with edit, delete, restore buttons.
     */
    actions({ onEdit, onDelete, onRestore, options = {} }) {
      cols.push({
        key: 'actions',
        label: 'Aksi',
        ...options,
        render: (row) => (
          <ActionColumn
            row={row}
            onEdit={onEdit}
            onDelete={onDelete}
            onRestore={onRestore}
          />
        ),
      });
      return api;
    },

    build() {
      return cols;
    },
  };

  return api;
}

/**
 * StatusBadge — renders a colored badge for status values.
 */
const statusColors = {
  active: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
  inactive: 'bg-slate-500/10 text-slate-400 border-slate-500/20',
  pending: 'bg-amber-500/10 text-amber-400 border-amber-500/20',
  used: 'bg-blue-500/10 text-blue-400 border-blue-500/20',
  damaged: 'bg-red-500/10 text-red-400 border-red-500/20',
  lost: 'bg-red-500/10 text-red-400 border-red-500/20',
  deleted: 'bg-red-500/10 text-red-400 border-red-500/20',
  verified: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
  unverified: 'bg-amber-500/10 text-amber-400 border-amber-500/20',
  default: 'bg-slate-500/10 text-slate-400 border-slate-500/20',
};

export function StatusBadge({ value }) {
  const color = statusColors[value?.toLowerCase()] || statusColors.default;
  return (
    <span
      className={cn(
        'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium capitalize',
        color
      )}
    >
      {value || '—'}
    </span>
  );
}

/**
 * ActionColumn — renders edit, delete, restore action buttons.
 */
export function ActionColumn({ row, onEdit, onDelete, onRestore }) {
  return (
    <div className="flex items-center gap-1">
      {onEdit && (
        <button
          onClick={(e) => { e.stopPropagation(); onEdit(row); }}
          className="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-800 hover:text-cyan-400"
          title="Edit"
        >
          <Pencil className="h-4 w-4" />
        </button>
      )}
      {onDelete && (
        <button
          onClick={(e) => { e.stopPropagation(); onDelete(row); }}
          className="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-800 hover:text-red-400"
          title="Hapus"
        >
          <Trash2 className="h-4 w-4" />
        </button>
      )}
      {onRestore && (
        <button
          onClick={(e) => { e.stopPropagation(); onRestore(row); }}
          className="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-800 hover:text-emerald-400"
          title="Pulihkan"
        >
          <RotateCcw className="h-4 w-4" />
        </button>
      )}
    </div>
  );
}

/**
 * Format date string to Indonesian locale.
 */
export function formatDate(dateStr) {
  if (!dateStr) return '—';
  try {
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  } catch {
    return dateStr;
  }
}
