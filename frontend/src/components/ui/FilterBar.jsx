import { RotateCcw } from 'lucide-react';
import { cn } from '../../utils/cn';

/**
 * FilterBar — renders a row of filter inputs with a reset button.
 *
 * Usage:
 *   <FilterBar
 *     filters={[
 *       { key: 'status', label: 'Status', type: 'select', options: [
 *         { value: 'active', label: 'Active' },
 *         { value: 'inactive', label: 'Inactive' },
 *       ]},
 *       { key: 'site_id', label: 'Site', type: 'text' },
 *     ]}
 *     values={filters}
 *     onChange={(key, value) => setFilters({ ...filters, [key]: value })}
 *     onReset={resetFilters}
 *   />
 */
export function FilterBar({ filters = [], values = {}, onChange, onReset, className }) {
  const hasActiveFilters = Object.values(values).some(
    (v) => v !== null && v !== undefined && v !== ''
  );

  if (!filters.length) return null;

  return (
    <div className={cn('flex flex-wrap items-end gap-3', className)}>
      {filters.map((filter) => (
        <div key={filter.key} className="min-w-[160px]">
          {filter.label && (
            <label className="mb-1 block text-xs font-medium text-slate-500">
              {filter.label}
            </label>
          )}
          {filter.type === 'select' ? (
            <select
              value={values[filter.key] ?? ''}
              onChange={(e) => onChange(filter.key, e.target.value)}
              className="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
            >
              <option value="">Semua</option>
              {filter.options?.map((opt) => (
                <option key={opt.value} value={opt.value}>
                  {opt.label}
                </option>
              ))}
            </select>
          ) : (
            <input
              type={filter.type || 'text'}
              value={values[filter.key] ?? ''}
              onChange={(e) => onChange(filter.key, e.target.value)}
              placeholder={filter.placeholder || `Cari ${filter.label?.toLowerCase() || ''}`}
              className="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
            />
          )}
        </div>
      ))}
      {hasActiveFilters && (
        <button
          onClick={onReset}
          className="flex items-center gap-1.5 rounded-lg border border-slate-700 px-3 py-2 text-sm text-slate-400 transition hover:bg-slate-800 hover:text-slate-200"
        >
          <RotateCcw className="h-3.5 w-3.5" />
          Reset
        </button>
      )}
    </div>
  );
}
