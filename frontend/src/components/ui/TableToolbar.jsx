import { Plus, RefreshCw } from 'lucide-react';
import { SearchInput } from './SearchInput';
import { cn } from '../../utils/cn';

/**
 * TableToolbar — top toolbar for CRUD list pages.
 *
 * Usage:
 *   <TableToolbar
 *     search={crud.search}
 *     onSearch={crud.setSearch}
 *     onRefresh={crud.refresh}
 *     onCreate={() => navigate('/users/create')}
 *     createLabel="Tambah User"
 *     createPermission="create-user"
 *   />
 */
export function TableToolbar({
  search,
  onSearch,
  onRefresh,
  onCreate,
  createLabel = 'Tambah',
  createPermission,
  hasPermission,
  searchPlaceholder = 'Cari...',
  children,
  className,
}) {
  const canCreate = createPermission ? hasPermission?.(createPermission) : true;

  return (
    <div className={cn('mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between', className)}>
      <div className="flex flex-1 items-center gap-3">
        <SearchInput
          value={search}
          onChange={onSearch}
          placeholder={searchPlaceholder}
          className="w-full max-w-xs"
        />
        {onRefresh && (
          <button
            onClick={onRefresh}
            className="rounded-lg p-2 text-slate-500 transition hover:bg-slate-800 hover:text-slate-300"
            title="Refresh"
          >
            <RefreshCw className="h-4 w-4" />
          </button>
        )}
      </div>
      <div className="flex items-center gap-3">
        {children}
        {onCreate && canCreate && (
          <button
            onClick={onCreate}
            className="inline-flex items-center gap-2 rounded-lg bg-cyan-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-cyan-600"
          >
            <Plus className="h-4 w-4" />
            {createLabel}
          </button>
        )}
      </div>
    </div>
  );
}
