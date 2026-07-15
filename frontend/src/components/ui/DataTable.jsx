import { cn } from '../../utils/cn';
import { Loading } from './Loading';
import { EmptyState } from './EmptyState';
import { Inbox } from 'lucide-react';

export function DataTable({
  columns,
  data,
  loading,
  onRowClick,
  emptyTitle,
  emptyDescription,
  emptyAction,
}) {
  if (loading) {
    return <Loading />;
  }

  if (!data || data.length === 0) {
    return (
      <EmptyState
        icon={Inbox}
        title={emptyTitle}
        description={emptyDescription}
        action={emptyAction}
      />
    );
  }

  return (
    <div className="overflow-x-auto rounded-lg border border-slate-800">
      <table className="w-full text-left text-sm">
        <thead>
          <tr className="border-b border-slate-800 bg-slate-900">
            {columns.map((col) => (
              <th
                key={col.key}
                className={cn('px-4 py-3 font-medium text-slate-400', col.className)}
                style={col.width ? { width: col.width } : undefined}
              >
                {col.label}
              </th>
            ))}
          </tr>
        </thead>
        <tbody className="divide-y divide-slate-800">
          {data.map((row, rowIndex) => (
            <tr
              key={row.id ?? rowIndex}
              onClick={() => onRowClick?.(row)}
              className={cn(
                'transition',
                onRowClick ? 'cursor-pointer hover:bg-slate-800/50' : 'hover:bg-slate-900'
              )}
            >
              {columns.map((col) => (
                <td key={col.key} className={cn('px-4 py-3 text-slate-300', col.className)}>
                  {col.render ? col.render(row) : row[col.key]}
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
